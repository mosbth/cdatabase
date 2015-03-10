<?php

require "../src/Database/TSQLQueryBuilderBasic.php";
require "../src/Database/CDatabaseBasic.php";

$db = new \Mos\Database\CDatabaseBasic();



//
// Create a table
//
$db->setTablePrefix('mos_');

$db->createTable(
    'test',
    [
        'id'    => ['integer', 'primary key', 'not null'],
        'age'   => ['integer'],
        'text'  => ['varchar(20)'],
        'text2' => ['varchar(20)']
    ]
);

echo "<pre>" . $db->getSQL() . "</pre>";



//
// Insert a single row into table using key => value
//
$db->insert(
    'test',
    [
        'id' => 2,
        'text' => "Mumintrollet",
        'text2' => "Mumindalen",
    ]
);

echo "<pre>" . $db->getSQL() . "</pre>";



//
// Insert a single row into table using two arrays
//
$db->insert(
    'test',
    ['id', 'text', 'text2'],
    [2, "Mumintrollet", "Mumindalen"]
);

echo "<pre>" . $db->getSQL() . "</pre>";



//
// Insert a single row into table using one array (rest will be sent as parameters)
//
$db->insert(
    'test',
    ['id', 'text', 'text2']
);

echo "<pre>" . $db->getSQL() . "</pre>";



//
// Update a single row using key => value
//
$db->update(
    'test',
    [
        'age' => 22,
        'text' => "Mumintrollet",
        'text2' => "Mumindalen",
    ],
    "id = 2"
);

echo "<pre>" . $db->getSQL() . "</pre>";



//
// Update a single row using two arrays
//
$db->update(
    'test',
    ['age', 'text', 'text1'],
    [22, "Mumintrollet", "Mumindalen"],
    "id = 2"
);

echo "<pre>" . $db->getSQL() . "</pre>";



//
// Update a single row into table using one array (rest will be sent as parameters)
//
$db->update(
    'test',
    ['age', 'text', 'text1'],
    "id = ?"
);

echo "<pre>" . $db->getSQL() . "</pre>";



//
// Select from database
//
$db->select("age, text, text1")
    ->from('test')
    ->where("id = 2")
;

echo "<pre>" . $db->getSQL() . "</pre>";



$db->orderBy("id ASC");

echo "<pre>" . $db->getSQL() . "</pre>";


$db->groupBy("age ASC");

echo "<pre>" . $db->getSQL() . "</pre>";



//
// Select and join from database
//
$db->select("t1.*, t2.*")
    ->from('test AS t1')
    ->join('test AS t2', 't1.id = t2.id')
;

echo "<pre>" . $db->getSQL() . "</pre>";



$db->select("t1.*, t2.*, t3.*")
    ->from('test AS t1')
    ->join('test AS t2', 't1.id = t2.id')
    ->join('test AS t3', 't1.id = t3.id');

echo "<pre>" . $db->getSQL() . "</pre>";


$db->select("t1.*, t2.id AS id2, t3.id AS id3")
    ->from('test AS t1')
    ->rightJoin('test AS t2', 't1.id = t2.id')
    ->rightJoin('test AS t3', 't1.id = t3.id');

echo "<pre>" . $db->getSQL() . "</pre>";

$db->select("t1.*, t2.id AS id2, t3.id AS id3")
    ->from('test AS t1')
    ->leftJoin('test AS t2', 't1.id = t2.id')
    ->leftJoin('test AS t3', 't1.id = t3.id');

echo "<pre>" . $db->getSQL() . "</pre>";

//
// Select, limit and offset
//
$db->select("*")
   ->from('test')
   ->limit('1');

echo "<pre>" . $db->getSQL() . "</pre>";


$db->select("*")
   ->from('test')
   ->limit('2');

echo "<pre>" . $db->getSQL() . "</pre>";


$db->select("*")
   ->from('test')
   ->limit('1')
   ->offset('2');

echo "<pre>" . $db->getSQL() . "</pre>";



//
// Delete a single row
//
$db->delete(
    'test',
    "id = 2"
);

echo "<pre>" . $db->getSQL() . "</pre>";



//
// Delete all row
//
$db->delete(
    'test'
);

echo "<pre>" . $db->getSQL() . "</pre>";



//
// Drop a table
//
$db->dropTable('test');

echo "<pre>" . $db->getSQL() . "</pre>";
