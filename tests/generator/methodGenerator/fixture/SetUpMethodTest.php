<?php

use PHPUnit\Framework\TestCase;
use Unitgen\generator\methodGenerator\fixture\SetUpMethod;

class SetUpMethodTest extends TestCase
{
    /**
     * @var SetUpMethod
     */
    private $setUpMethod;

    public function setUp()
    {
        $this->setUpMethod = new SetUpMethod(
            new \ReflectionClass(stdClass::class)
        );
    }

    public function testGenerate()
    {
        $result = "    public function setUp()
    {
        \$this->stdClass = new \\stdClass();
    }

";
        $this->assertEquals($result, $this->setUpMethod->generate());
    }
}