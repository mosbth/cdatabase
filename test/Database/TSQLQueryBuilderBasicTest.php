<?php

namespace Mos\Database;

/**
* A testclass
*
*/
class TSQLQueryBuilderBasicTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the mock
     */
    protected function setUp()
    {
        $this->mock = $this->getMockForTrait('Mos\Database\TSQLQueryBuilderBasic');
        $this->mock->setTablePrefix('mos_');
    }

    /**
     * Test
     *
     * @return void
     *
     */
    public function testGetName()
    {
        // $mock = $this->getMockForTrait('Mos\Database\TSQLQueryBuilderBasic');

        // $mock->setTablePrefix('mos_');
        $this->mock->createTable(
        'test',
        [
            'id'    => ['integer', 'primary key', 'not null'],
            'age'   => ['integer'],
            'text'  => ['varchar(20)'],
            'text2' => ['varchar(20)']
            ]
        );

        $res = $this->mock->getSQL();
        $exp = <<<EOD
CREATE TABLE mos_test
(
\tid integer primary key not null,
\tage integer,
\ttext varchar(20),
\ttext2 varchar(20)
);

EOD;

        $this->assertEquals($res, $exp, "The SQL for create table does not match.");
    }


    public function testRightJoin()
    {
        $this->mock->select("t1.*, t2.id AS id2, t3.id AS id3")
                    ->from('test AS t1')
                    ->rightJoin('test AS t2', 't1.id = t2.id')
                    ->rightJoin('test AS t3', 't1.id = t3.id');

        /*INSERT INTO mos_test
        (id, text, text2)
        VALUES
        (2, 'Mumintrollet', 'Mumindalen');*/
        $res = $this->mock->getSQL();

        $exp = <<<EOD
SELECT
\tt1.*, t2.id AS id2, t3.id AS id3
FROM mos_test AS t1
RIGHT JOIN mos_test AS t2
\tON t1.id = t2.id
RIGHT JOIN mos_test AS t3
\tON t1.id = t3.id
;
EOD;
        $this->assertEquals($res, $exp, "The SQL for right join does not match.");
    }

    public function testLeftJoin()
    {
        $this->mock->select("t1.*, t2.id AS id2, t3.id AS id3")
                    ->from('test AS t1')
                    ->leftJoin('test AS t2', 't1.id = t2.id')
                    ->leftJoin('test AS t3', 't1.id = t3.id');

        $res = $this->mock->getSQL();

        $exp = <<<EOD
SELECT
\tt1.*, t2.id AS id2, t3.id AS id3
FROM mos_test AS t1
LEFT JOIN mos_test AS t2
\tON t1.id = t2.id
LEFT JOIN mos_test AS t3
\tON t1.id = t3.id
;
EOD;
        $this->assertEquals($res, $exp, "The SQL for right join does not match.");
    }

    public function testInnerJoin()
    {
        $this->mock->select("t1.*, t2.id AS id2, t3.id AS id3")
                    ->from('test AS t1')
                    ->join('test AS t2', 't1.id = t2.id')
                    ->join('test AS t3', 't1.id = t3.id');

        $res = $this->mock->getSQL();

        $exp = <<<EOD
SELECT
\tt1.*, t2.id AS id2, t3.id AS id3
FROM mos_test AS t1
INNER JOIN mos_test AS t2
\tON t1.id = t2.id
INNER JOIN mos_test AS t3
\tON t1.id = t3.id
;
EOD;
        $this->assertEquals($res, $exp, "The SQL for right join does not match.");
    }

}
