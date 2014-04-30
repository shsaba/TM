<?php

// Local
$app['locale'] = 'fr';
$app['session.default_locale'] = $app['locale'];
$app['translator.messages'] = array(
    'fr' => __DIR__ . '/../resources/locales/fr.yml',
);


// Doctrine (db)
$app['db.options'] = array(
    'driver' => 'pdo_mysql',
    'host' => 'localhost',
    'dbname' => 'tasksmanager',
    'user' => 'root',
    'password' => '12101989',
);
