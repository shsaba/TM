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
$kindsTasks->addColumn('category_id', 'integer', array('unsigned' => true));
$kindsTasks->addForeignKeyConstraint($categories, array('category_id'), array('id'), array('onDelete' => "CASCADE"));


$report = $schema->createTable('reports');
$report->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
$report->setPrimaryKey(array('id'));
$report->addColumn('note', 'text');
$report->addColumn('date', 'date');


$tasksReport = $schema->createTable('tasksReport');
$tasksReport->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
$tasksReport->setPrimaryKey(array('id'));
$tasksReport->addColumn('title', 'string', array('length' => 255));
$tasksReport->addColumn('content', 'text');
$tasksReport->addColumn('category', 'text');
$tasksReport->addColumn('kindTask', 'text');
$tasksReport->addColumn('task', 'text');
$tasksReport->addColumn('category_id', 'integer', array('unsigned' => true));
$tasksReport->addColumn('kindTask_id', 'integer', array('unsigned' => true));
$tasksReport->addColumn('task_id', 'integer', array('unsigned' => true));
$tasksReport->addForeignKeyConstraint($categories, array('category_id'), array('id'));
$tasksReport->addForeignKeyConstraint($kindsTasks, array('kindTask_id'), array('id'));

return $schema;