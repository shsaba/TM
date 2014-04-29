<?php

$schema = new \Doctrine\DBAL\Schema\Schema();

$post = $schema->createTable('tasks');
$post->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
$post->addColumn('title', 'string', array('length' => 32));
$post->addColumn('content', 'string');
$post->setPrimaryKey(array('id'));

return $schema;