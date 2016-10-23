<?php

use PHPUnit\Framework\TestCase;
use Unitgen\annotation\AnnotationParser;
use Unitgen\generator\classGenerator\TestClass;
use Unitgen\generator\factory\TestGeneratorFactory;
use Unitgen\generator\GeneratorCompose;
use Unitgen\generator\methodGenerator\fixture\SetUpMethod;
use Unitgen\generator\methodGenerator\fixture\TearDownMethod;
use Unitgen\generator\methodGenerator\test\ExceptionMethod;
use Unitgen\generator\methodGenerator\test\TestMethod;

class TestGeneratorFactoryTest extends TestCase
{
    /**
     * @var TestGeneratorFactory
     */
    private $testGeneratorFactory;

    public function setUp()
    {
        $this->testGeneratorFactory = new TestGeneratorFactory();
    }

    public function testGetClassGenerator()
    {
        $this->assertInstanceOf(
            TestClass::class,
            $this->testGeneratorFactory->getClassGenerator(stdClass::class)
        );
    }

    public function testGetMethodGenerator()
    {
        $this->assertInstanceOf(
            TestMethod::class,
            $this->testGeneratorFactory->getMethodGenerator(
                new \ReflectionMethod(StubGeneratorClass::class, 'methodA')
            )
        );
    }

    public function testGetExceptionMethodGenerator()
    {
        $annotationMock = $this->getMockBuilder(AnnotationParser::class)
            ->setMethods
            (['getTagValue'])->getMock();

        $annotationMock
            ->method('getTagValue')
            ->willReturn(\Exception::class)
            ->with($this->equalTo('throws'));

        $method = (new \ReflectionClass(StubGeneratorClass::class))
            ->getMethod('methodA');

        $this->assertInstanceOf(
            ExceptionMethod::class,
            $this->testGeneratorFactory->getExceptionMethodGenerator(
                $method, $annotationMock
            )
        );
    }

    public function testGetSetUpMethodGenerator()
    {
        $this->assertInstanceOf(
            SetUpMethod::class,
            $this->testGeneratorFactory->getSetUpMethodGenerator(
                new \ReflectionClass(StubGeneratorClass::class)
            )
        );
    }

    public function testGetTearDownMethodGenerator()
    {
        $this->assertInstanceOf(
            TearDownMethod::class,
            $this->testGeneratorFactory->getTearDownMethodGenerator()
        );
    }

    public function testGetGeneratorCompose()
    {
        $this->assertInstanceOf(
            GeneratorCompose::class,
            $this->testGeneratorFactory->getGeneratorCompose()
        );
    }
}

class StubGeneratorClass {
    public function methodA() {}
}