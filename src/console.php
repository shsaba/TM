<?php

use Doctrine\DBAL\DriverManager;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

$console = new Application('Silex - TasksManager', '1.0');

$app->boot();

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



return $console;
