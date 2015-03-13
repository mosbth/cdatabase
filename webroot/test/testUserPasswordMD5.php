<?php

//
// Set the error reporting.
//
error_reporting(-1);              // Report all type of errors
ini_set('output_buffering', 0);   // Do not buffer outputs, write directly


//
// Get required files
//
require "../../src/Database/TSQLQueryBuilderBasic.php";
require "../../src/Database/CDatabaseBasic.php";

$db = new \Mos\Database\CDatabaseBasic();


//
// Read config file
//
$options = require "../config_mysql.php";



//
// Carry out som tests, db must exist
//
$db->setOptions($options);
$db->setTablePrefix($options['table_prefix']);
$db->connect();



//
// Drop a table if it exists
//
$tableName = 'test';
$db->dropTableIfExists($tableName)
   ->execute();


//
// Create a table
//
$db->createTable(
    $tableName,
    [
        'id'       => ['integer', 'auto_increment', 'primary key', 'not null'],
        'username' => ['varchar(20)'],
        'password' => ['varchar(32)'],
        'salt'     => ['varchar(32)'],
    ]
)->execute();



//
// Add some users to test with
//
$sql = <<<EOD
INSERT INTO
    {$options['table_prefix']}$tableName (username, salt)
    VALUES (?, md5(NOW()));
EOD;

$db->execute($sql, ['doe']);
$db->execute($sql, ['admin']);



//
// Add some users, two steps since must use salt as created before.
//
$sql = <<<EOD
UPDATE
    {$options['table_prefix']}$tableName
    SET
        password = md5(concat(salt, ?))
    WHERE
        username = ?;
EOD;

$db->execute($sql, ['doe', 'doe']);
$db->execute($sql, ['admin', 'admin']);



//
// Check whats in the db
//
$db->select("*")
   ->from($tableName);

$res = $db->executeFetchAll();

echo "<pre>" . print_r($res, 1) . "</pre>";



//
// Check password for each user
//
$db->select("username")
   ->from($tableName)
   ->where("username = ?")
   ->andWhere("password = md5(concat(salt, ?))");

$res = $db->executeFetchAll(['doe', 'doe']);

echo "<pre>" . print_r($res, 1) . "</pre>";


//
// Check password for each user
//
$db->select("username")
   ->from($tableName)
   ->where("username = ?")
   ->andWhere("password = md5(concat(salt, ?))");

$res = $db->executeFetchAll(['admin', 'admin']);

echo "<pre>" . print_r($res, 1) . "</pre>";



//
// Check (wrong) password for user
//
$db->select("username")
   ->from($tableName)
   ->where("username = ?")
   ->andWhere("password = md5(concat(salt, ?))");

$res = $db->executeFetchAll(['doe', 'admin']);

echo "<pre>" . print_r($res, 1) . "</pre>";
