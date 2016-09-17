<?php

class FooTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitGuy
     */
    protected $guy;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testMe()
    {

        $this->assertEquals(1, 1);

    }
}
