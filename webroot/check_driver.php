<?php

echo "Check if PDO are available on this system.<br><br>";

$not = class_exists('PDO') ? null : "NOT";
echo "PDO class is $not available.<br><br>";

$not = extension_loaded('pdo') ? null : "NOT";
echo "PDO extension is $not loaded.<br><br>";

$not = extension_loaded('pdo_sqlite') ? null : "NOT";
echo "PDO SQLite extension is $not loaded.<br><br>";

$not = extension_loaded('pdo_mysql') ? null : "NOT";
echo "PDO MySQL extension is $not loaded.<br><br>";

echo "PDO has the following available drivers:<br>";
print_r(PDO::getAvailableDrivers());
