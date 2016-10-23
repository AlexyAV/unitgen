<?php

use PHPUnit\Framework\TestCase;
use Unitgen\generator\methodGenerator\test\TestMethod;

class TestMethodTest extends TestCase
{
    /**
     * @var TestMethod
     */
    private $testMethod;

    public function setUp()
    {
        $method = (new \ReflectionClass(StubClass::class))->getMethod(
            'methodA'
        );

        $this->testMethod = new TestMethod($method);
    }

    public function testSetTargetMethodName()
    {
        $this->assertEquals(
            'methodB',
            $this->testMethod->setTargetMethodName(
                (new \ReflectionClass(StubClass::class))->getMethod('methodB')
            )
        );
    }

    public function testGenerate()
    {
        $result = "    public function testMethodA()
    {
        //TODO Implement test for methodA method
        \$this->assertTrue(false);
    }

";
        $this->assertEquals($result, $this->testMethod->generate());
    }
}

class StubClass
{
    public function methodA(){}
    public function methodB(){}
}