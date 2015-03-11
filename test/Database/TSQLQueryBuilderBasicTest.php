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

        $res = $this->mock->getSQL();

        $exp = <<<EOD
SELECT
\tt1.*, t2.id AS id2, t3.id AS id3
FROM mos_test AS t1
RIGHT OUTER JOIN mos_test AS t2
\tON t1.id = t2.id
RIGHT OUTER JOIN mos_test AS t3
\tON t1.id = t3.id
;
EOD;
        $this->assertEquals($res, $exp);
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
LEFT OUTER JOIN mos_test AS t2
\tON t1.id = t2.id
LEFT OUTER JOIN mos_test AS t3
\tON t1.id = t3.id
;
EOD;
        $this->assertEquals($res, $exp);
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
        $this->assertEquals($res, $exp);
    }

    public function testDelete()
    {
        $this->mock->delete('test');

        $res = $this->mock->getSQL();

        $exp = "DELETE FROM mos_test;\n";

        $this->assertEquals($res, $exp);
    }

    public function testSelectLimitOffset()
    {
        $this->mock->select("*")
                   ->from('test')
                   ->limit('1')
                   ->offset('2');

        $res = $this->mock->getSQL();

        $exp = <<<EOD
SELECT
\t*
FROM mos_test
LIMIT \n\t1
OFFSET \n\t2
;
EOD;


        $this->assertEquals($res, $exp);
    }

    public function testWhereAndWhere()
    {
        $this->mock->select("*")
                   ->from('test')
                   ->where('id = 1')
                   ->andWhere('name = mumin');

        $res = $this->mock->getSQL();

        $exp = <<<EOD
SELECT
\t*
FROM mos_test
WHERE \n\t(id = 1)
\tAND (name = mumin)
;
EOD;

        $this->assertEquals($res, $exp);
    }
    public function testDeleteWhere()
    {
        $this->mock->delete('test', "id = 2");

        $res = $this->mock->getSQL();

        $exp = "DELETE FROM mos_test WHERE id = 2;\n";

        $this->assertEquals($res, $exp);
    }

    public function testDropTable()
    {
        $this->mock->dropTable('test');

        $res = $this->mock->getSQL();

        $exp = "DROP TABLE mos_test;\n";

        $this->assertEquals($res, $exp);
    }

    public function testdropTableIfExists()
    {
        $this->mock->dropTableIfExists('test');

        $res = $this->mock->getSQL();

        $exp = "DROP TABLE IF EXISTS mos_test;\n";

        $this->assertEquals($res, $exp);
    }


    public function testGroupBy()
    {
        $this->mock->select()
                    ->from('test')
                    ->groupBy('test');


        $res = $this->mock->getSQL();
        $exp = <<<EOD
SELECT
\t*
FROM mos_test
GROUP BY test
;
EOD;
        $this->assertEquals($res, $exp);
    }

    public function testOrderBy()
    {
        $this->mock->select()
                    ->from('test')
                    ->orderBy('test');


        $res = $this->mock->getSQL();
        $exp = <<<EOD
SELECT
\t*
FROM mos_test
ORDER BY test
;
EOD;
        $this->assertEquals($res, $exp);
    }


    public function testInsertSingleRow()
    {
        $this->mock->insert(
            'test',
            [
                'id' => 2,
                'text' => "Mumintrollet",
                'text2' => "Mumindalen",
            ]
        );

        $res = $this->mock->getSQL();

        $exp = <<<EOD
INSERT INTO mos_test
\t(id, text, text2)
\tVALUES
\t(2, 'Mumintrollet', 'Mumindalen');

EOD;
        $this->assertEquals($res, $exp);
    }

    public function testInsertSingleRowTwoArray()
    {
        $this->mock->insert(
            'test',
            ['id', 'text', 'text2'],
            [2, "Mumintrollet", "Mumindalen"]
        );

        $res = $this->mock->getSQL();

        $exp = <<<EOD
INSERT INTO mos_test
\t(id, text, text2)
\tVALUES
\t(2, 'Mumintrollet', 'Mumindalen');

EOD;
        $this->assertEquals($res, $exp);
    }

    public function testInsertSingleRowNoValues()
    {
        $this->mock->insert(
            'test',
            ['id', 'text', 'text2']
        );

        $res = $this->mock->getSQL();

        $exp = <<<EOD
INSERT INTO mos_test
\t(id, text, text2)
\tVALUES
\t(?, ?, ?);

EOD;
        $this->assertEquals($res, $exp);
    }


    public function testInsertWrongData()
    {
        try {
            $this->mock->insert(
                'test',
                ['id', 'text', 'text2'],
                [2, "Mumintrollet"]
            );
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }


    public function testUpdateTwoArrays()
    {
        $this->mock->update(
            'test',
            ['age', 'text', 'text1'],
            [22, "Mumintrollet", "asd"],
            "id = ?"
        );

        $res = $this->mock->getSQL();

        $exp = <<<EOD
UPDATE mos_test
SET
\tage = 22,
\ttext = 'Mumintrollet',
\ttext1 = 'asd'
WHERE id = ?
;

EOD;
        $this->assertEquals($res, $exp);
    }

    public function testUpdateWrongData()
    {
        try {
            $this->mock->update(
                'test',
                ['age', 'text', 'text1'],
                [22, "Mumindalen"],
                "id = 2"
            );
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }
}
