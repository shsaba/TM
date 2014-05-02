<?php

use Doctrine\DBAL\DriverManager;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

$console = new Application('Silex - TasksManager', '1.0');

$app->boot();


$console
        ->register('assetic:dump')
        ->setDescription('Dumps all assets to the filesystem')
        ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
            if (!$app['assetic.enabled']) {
                return false;
            }

            $dumper = $app['assetic.dumper'];
            if (isset($app['twig'])) {
                $dumper->addTwigAssets();
            }
            $dumper->dumpAssets();
            $output->writeln('<info>Dump finished</info>');
        })
;

if (isset($app['cache.path'])) {
    $console
            ->register('cache:clear')
            ->setDescription('Clears the cache')
            ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {

                $cacheDir = $app['cache.path'];
                $finder = Finder::create()->in($cacheDir)->notName('.gitkeep');

                $filesystem = new Filesystem();
                $filesystem->remove($finder);

                $output->writeln(sprintf("%s <info>success</info>", 'cache:clear'));
            });
}


$console
        ->register('doctrine:schema:show')
        ->setDescription('Output schema declaration')
        ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
            $schema = require __DIR__ . '/../resources/db/schema.php';

            foreach ($schema->toSql($app['db']->getDatabasePlatform()) as $sql) {
                $output->writeln($sql . ';');
            }
        })
;


$console
        ->register('doctrine:schema:load')
        ->setDescription('Load schema')
        ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
            $schema = require __DIR__ . '/../resources/db/schema.php';

            foreach ($schema->toSql($app['db']->getDatabasePlatform()) as $sql) {
                $app['db']->exec($sql . ';');
            }
        })
;

$console
        ->register('doctrine:database:create')
        ->setDescription('Creates the configured databases')
        ->addOption('connection', null, InputOption::VALUE_OPTIONAL, 'The connection to use for this command')
        ->setHelp(
                <<<EOT
The <info>doctrine:database:create</info> command creates the default
connections database:

<info>php app/console doctrine:database:create</info>

You can also optionally specify the name of a connection to create the
database for:

<info>php app/console doctrine:database:create --connection=default</info>
EOT
        )
        ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {
            $connection = $app['db'];

            $params = $connection->getParams();
            $name = isset($params['path']) ? $params['path'] : $params['dbname'];

            unset($params['dbname']);

            $tmpConnection = DriverManager::getConnection($params);

            // Only quote if we don't have a path
            if (!isset($params['path'])) {
                $name = $tmpConnection->getDatabasePlatform()->quoteSingleIdentifier($name);
            }

            $error = false;
            try {
                $tmpConnection->getSchemaManager()->createDatabase($name);
                $output->writeln(sprintf('<info>Created database for connection named <comment>%s</comment></info>', $name));
            } catch (\Exception $e) {
                $output->writeln(sprintf('<error>Could not create database for connection named <comment>%s</comment></error>', $name));
                $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
                $error = true;
            }

            $tmpConnection->close();

            return $error ? 1 : 0;
        })
;

return $console;
