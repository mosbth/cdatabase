<?php

namespace Mos\Database;

/**
* A testclass
*
*/
class CDatabaseBasicTest extends \PHPUnit_Framework_TestCase
{
    private $mysqlOptions = [
        // Set up details on how to connect to the database
        'dsn'     => "mysql:host=localhost;dbname=test;",
        'username'        => "root",
        'password'        => "",
        'driver_options'  => [\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"],
        'table_prefix'    => "test_",
        'verbose' => true,
    ];

    private $sqliteOptions = [
        'dsn' => "sqlite:memory::",
        "verbose" => false
    ];


    private $rows = [
        [22, "Mumintrollet"],
        [44, "Mumindalen"],
        [66, "Lilla My"],
    ];

    private $db;

    protected function setUp()
    {
        $this->db = new \Mos\Database\CDatabaseBasic();
        $this->db->setOptions($this->sqliteOptions);
        $this->db->connect();
        $this->selectSQL = $this->db->select("id, age, text")
                                    ->from('test')->getSQL();
    }

    public function testCreateObject()
    {
        $db = new \Mos\Database\CDatabaseBasic();

        $this->isInstanceOf('\Mos\Database\CDatabaseBasic');
    }

    public function testConnect()
    {
        $this->db->connect();
    }

    public function testConnectGetException()
    {
        $this->db->setOptions([]);
        try {
            $this->db->connect();
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }

    }

    public function testCreateTable()
    {
        $this->db->createTable(
            'test',
            [
                'id'    => ['integer', 'auto_increment', 'primary key', 'not null'],
                'age'   => ['integer'],
                'text'  => ['varchar(20)'],
            ]
        );

        $this->db->execute();
    }

    public function testInsertSingleRow()
    {
        $this->db->insert(
            'test',
            [
                'age'  => $this->rows[0][0],
                'text' => $this->rows[0][1],
            ]
        );

        $this->db->execute();
    }

    public function testInsertAsArray()
    {
        $this->db->insert(
            'test',
            ['age', 'text'],
            $this->rows[0]
        );

        $this->db->execute();
    }

    public function testUpdateRow()
    {
        $this->db->update(
            'test',
            [
                'age' => '?',
                'text' => '?',
            ],
            "id = ?"
        );
        $id2 = $this->db->lastInsertId();
        $this->db->execute(array_merge($this->rows[1], [$id2]));
    }

    public function testDropTable()
    {
        $this->db->dropTable('test');
        $this->db->execute();
    }
}
