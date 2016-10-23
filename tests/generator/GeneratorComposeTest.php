<?php

use PHPUnit\Framework\TestCase;
use Unitgen\generator\GeneratorCompose;
use \Unitgen\generator\classGenerator\TestClass;

class GeneratorComposeTest extends TestCase
{
    /**
     * @var GeneratorCompose
     */
    private $generatorCompose;

    public function setUp()
    {
        $this->generatorCompose = new GeneratorCompose;
    }

    public function testAddGenerator()
    {
        $generatorMock = $this->getMockBuilder(TestClass::class)
            ->disableOriginalConstructor()
            ->setMethods(['generate'])
            ->getMock();

        $this->generatorCompose->addGenerator($generatorMock);

        $this->assertEquals([$generatorMock],
            $this->generatorCompose->getGenerator());

        $this->assertFalse($this->generatorCompose->addGenerator(
            $this->generatorCompose)
        );
    }

    public function testGenerate()
    {
        $generatorMock = $this->getMockBuilder(TestClass::class)
            ->disableOriginalConstructor()
            ->setMethods(['generate'])
            ->getMock();

        $generatorMock->method('generate')->willReturn('generatedValue');

        $this->generatorCompose->addGenerator($generatorMock);

        $this->assertEquals(
            'generatedValue}',
            $this->generatorCompose->generate()
        );
    }
}