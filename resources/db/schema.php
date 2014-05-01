<?php

$schema = new \Doctrine\DBAL\Schema\Schema();

$categories = $schema->createTable('categories');
$categories->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
$categories->setPrimaryKey(array('id'));
$categories->addColumn('category', 'string', array('length' => 255));

$kindsTasks = $schema->createTable('kindsTasks');
$kindsTasks->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
$kindsTasks->setPrimaryKey(array('id'));
$kindsTasks->addColumn('kind', 'string', array('length' => 255));

$tasks = $schema->createTable('tasks');
$tasks->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
$tasks->setPrimaryKey(array('id'));
$tasks->addColumn('title', 'string', array('length' => 255));
$tasks->addColumn('kindTask_id', 'integer', array('unsigned' => true));
$tasks->addColumn('category_id', 'integer', array('unsigned' => true));
$tasks->addUniqueIndex(array('title'));
$tasks->addForeignKeyConstraint($categories, array('category_id'), array('id'), array('onDelete' => "CASCADE"));
$tasks->addForeignKeyConstraint($kindsTasks, array('kindTask_id'), array('id'), array('onDelete' => "CASCADE"));

$report = $schema->createTable('report');
$report->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
$report->setPrimaryKey(array('id'));
$report->addColumn('date', 'date');


$tasksReport = $schema->createTable('tasksReport');
$tasksReport->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
$tasksReport->setPrimaryKey(array('id'));
$tasksReport->addColumn('title', 'string', array('length' => 255));
$tasksReport->addColumn('content', 'text');
$tasksReport->addColumn('kindTask_id', 'integer', array('unsigned' => true));
$tasksReport->addColumn('report_id', 'integer', array('unsigned' => true));
$tasksReport->addForeignKeyConstraint($report, array('report_id'), array('id'), array('onDelete' => "CASCADE"));


return $schema;