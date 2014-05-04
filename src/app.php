<?php

use Silex\Provider\TranslationServiceProvider;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../resources/views',
    'twig.form.templates' => array('form_div_layout.html.twig', 'common/form_div_layout.html.twig'),
));


$app->register(new TranslationServiceProvider());
$app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
            $translator->addLoader('yaml', new YamlFileLoader());
            $translator->addResource('yaml', __DIR__ . '/../resources/locales/fr.yml', 'fr');

            return $translator;
        }));


$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'host' => '127.0.0.1',
        'dbname' => 'tasksmanager',
        'user' => 'root',
        'password' => '',
        'charset' => 'utf8',
    ),
));


$app->register(new FormServiceProvider());

$app->register(new Silex\Provider\ValidatorServiceProvider());

return $app;
