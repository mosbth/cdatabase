<?php
//
// Carry out som tests, db must exist
//
$db->setOptions($options);
$db->connect();
//$db->connect('debug');


//
// Drop a table if it exists
//
$db->dropTableIfExists('test')
   ->execute();


//
// Create a table
//
$db->createTable(
    'test',
    [
        'id'    => ['integer', 'auto_increment', 'primary key', 'not null'],
        'age'   => ['integer'],
        'text'  => ['varchar(20)'],
    ]
);

$db->execute();



//
// Rows to test with
//
$rows = [
    [22, "Mumintrollet"],
    [44, "Mumindalen"],
    [66, "Lilla My"],
  ];

//
// Insert a single row into table using key => value
//
$db->insert(
    'test',
    [
        'age'  => $rows[0][0],
        'text' => $rows[0][1],
    ]
);

$db->execute();

$id1 = $db->lastInsertId();
echo "<p>Last inserted id: $id1</p>";


//
// Insert a single row into table using two arrays
//
$db->insert(
    'test',
    ['age', 'text'],
    $rows[0]
);

$db->execute();

$id2 = $db->lastInsertId();
echo "<p>Last inserted id: $id2</p>";


//
// Insert a single row into table using single array
//
$db->insert(
'test',
['age', 'text']
);

$db->execute($rows[0]);

$id3 = $db->lastInsertId();
echo "<p>Last inserted id: $id3</p>";

$db->execute($rows[0]);


//
// Select from database
//
echo "<p>Database consist of rows looking like </p><pre>" . print_r($rows[0], 1) . "</pre>";

$db->select("id, age, text")
    ->from('test')
;

$res = $db->executeFetchAll();

$selectSQL = $db->getSQL();

echo "<pre>" . print_r($res, 1) . "</pre>";



//
// Update a single row using key => value
//
$db->update(
    'test',
    [
        'age' => '?',
        'text' => '?',
    ],
    "id = ?"
);

$db->execute(array_merge($rows[1], [$id2]));



//
// Select from database
//
echo "<p>The second row was updated.</p>";

$res = $db->executeFetchAll($selectSQL);

echo "<pre>" . print_r($res, 1) . "</pre>";



//
// Update a single row using one arrays
//
$db->update(
    'test',
    ['age', 'text'],
    "id = ?"
);

$db->execute(array_merge($rows[2], [$id3]));



//
// Select from database
//
echo "<p>The third row was updated.</p>";

$res = $db->executeFetchAll($selectSQL);

echo "<pre>" . print_r($res, 1) . "</pre>";



//
// Select from database
//
$db->select("age")
    ->from('test')
;

$res = $db->executeFetchAll();

echo "<pre>" . print_r($res, 1) . "</pre>";



$db->orderBy("age DESC");

$res = $db->executeFetchAll();

echo "<pre>" . print_r($res, 1) . "</pre>";



//
// Select from database
//
$db->select("SUM(age)")
   ->from('test')
;

$res = $db->executeFetchAll();

echo "<pre>" . print_r($res, 1) . "</pre>";



//
// Select and group by
//
$db->select("text, SUM(age) AS age")
   ->from('test')
   ->groupBy('text')
;

$res = $db->executeFetchAll();

echo "<pre>" . print_r($res, 1) . "</pre>";



//
// Select, limit and offset
//
$db->select("*")
   ->from('test')
   ->limit('1');

$res = $db->executeFetchAll();

echo "<pre>" . print_r($res, 1) . "</pre>";


$db->select("*")
   ->from('test')
   ->limit('2');

$res = $db->executeFetchAll();

echo "<pre>" . print_r($res, 1) . "</pre>";


$db->select("*")
   ->from('test')
   ->limit('1')
   ->offset('2');

$res = $db->executeFetchAll();

echo "<pre>" . print_r($res, 1) . "</pre>";



//
// Select and join from database
//
$db->select("t1.*, t2.id AS id2")
    ->from('test AS t1')
    ->join('test AS t2', 't1.id = t2.id')
;

$res = $db->executeFetchAll();

echo "<pre>" . print_r($res, 1) . "</pre>";


$db->select("t1.*, t2.id AS id2, t3.id AS id3")
    ->from('test AS t1')
    ->join('test AS t2', 't1.id = t2.id')
    ->join('test AS t3', 't1.id = t3.id');

$res = $db->executeFetchAll();

echo "<pre>" . print_r($res, 1) . "</pre>";


$db->select("t1.*, t2.id AS id2, t3.id AS id3")
    ->from('test AS t1')
    ->rightJoin('test AS t2', 't1.id = t2.id')
    ->rightJoin('test AS t3', 't1.id = t3.id');

try {
    $res = $db->executeFetchAll();
    echo "<pre>" . print_r($res, 1) . "</pre>";
} catch (Exception $e) {
    echo "EXCEPTION: " . $e->getMessage();
    echo "<br>READ MORE IN THE SQLITE MANUAL: https://www.sqlite.org/omitted.html";
    echo "<br>NOT A PROBLEM. CONTINUING ANYWAY.";
}


$db->select("t1.*, t2.id AS id2, t3.id AS id3")
    ->from('test AS t1')
    ->leftJoin('test AS t2', 't1.id = t2.id')
    ->leftJoin('test AS t3', 't1.id = t3.id');

$res = $db->executeFetchAll();

echo "<pre>" . print_r($res, 1) . "</pre>";



//
// Delete a single row
//
$db->delete(
    'test',
    "id = ?"
);

$res = $db->execute([$id2]);



//
// Select from database
//
$res = $db->executeFetchAll($selectSQL);

echo "<pre>" . print_r($res, 1) . "</pre>";



//
// Delete all row
//
$db->delete('test');

$res = $db->execute();



//
// Select from database
//
$res = $db->executeFetchAll($selectSQL);

echo "<pre>" . print_r($res, 1) . "</pre>";


//
// Drop a table
//
$db->dropTable('test');

$res = $db->execute();
