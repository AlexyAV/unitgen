<?php

use PHPUnit\Framework\TestCase;
use Unitgen\generator\methodGenerator\fixture\TearDownMethod;

class TearDownMethodTest extends TestCase
{
    /**
     * @var TearDownMethod
     */
    private $tearDownMethod;

    public function setUp()
    {
        $this->tearDownMethod = new TearDownMethod();
    }

    public function testGenerate()
    {
        $result = "    public function tearDown()
    {
        //TODO Implement tearDown method
    }

";
        $this->assertEquals($result, $this->tearDownMethod->generate());
    }
}