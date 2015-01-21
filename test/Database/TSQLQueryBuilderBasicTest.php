<?php

namespace Mos\Database;

/**
* A testclass
*
*/
class TSQLQueryBuilderBasicTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test
     *
     * @return void
     *
     */
    public function testGetName()
    {
    $mock = $this->getMockForTrait('Mos\Database\TSQLQueryBuilderBasic');

    $mock->setTablePrefix('mos_');
    $mock->createTable(
    'test',
    [
        'id'    => ['integer', 'primary key', 'not null'],
        'age'   => ['integer'],
        'text'  => ['varchar(20)'],
        'text2' => ['varchar(20)']
        ]
    );

    $res = $mock->getSQL();
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
}
