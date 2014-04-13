<?php

require "../src/Database/TSQLQueryBuilderBasic.php";
require "../src/Database/CDatabaseBasic.php";

$db = new \Mos\Database\CDatabaseBasic();


//
// Read config file
//
$options = require "config_mysql.php";



//
// Carry out som tests
//
require __DIR__ . '/testDatabaseQueries.php';
