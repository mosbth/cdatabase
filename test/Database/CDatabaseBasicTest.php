<?php

namespace Mos\Database;

/**
* A testclass
*
*/
class CDatabaseBasicTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Test
     *
     * @return void
     *
     */
    public function testCreateObject()
    {
        $db = new \Mos\Database\CDatabaseBasic();

        $this->isInstanceOf('\Mos\Database\CDatabaseBasic');
    }
}
