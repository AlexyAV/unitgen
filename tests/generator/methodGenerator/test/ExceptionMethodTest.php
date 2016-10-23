<?php

use PHPUnit\Framework\TestCase;
use Unitgen\annotation\AnnotationParser;
use Unitgen\generator\exceptions\MethodGeneratorException;
use Unitgen\generator\methodGenerator\test\ExceptionMethod;

class ExceptionMethodTest extends TestCase
{
    /**
     * @var ExceptionMethod
     */
    private $exceptionMethod;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $annotationMock;

    public function setUp()
    {
        $this->annotationMock = $this->getMockBuilder(AnnotationParser::class)
            ->setMethods
        (['getTagValue'])->getMock();

        $this->annotationMock
            ->method('getTagValue')
            ->willReturn(ExceptionStubClass::class)
            ->with($this->equalTo('throws'));

        $method = (new \ReflectionClass(ExceptionStubClass::class))
            ->getMethod('methodA');

        $this->exceptionMethod = new ExceptionMethod(
            $method, $this->annotationMock
        );
    }

    public function testSetExceptionClassName()
    {
        $this->assertEquals(
            ExceptionStubClass::class,
            $this->exceptionMethod->setExceptionClassName(
                ExceptionStubClass::class
            )
        );

        $this->annotationMock = $this->getMockBuilder(AnnotationParser::class)
            ->setMethods
            (['getTagValue'])->getMock();

        $this->annotationMock
            ->method('getTagValue')
            ->willReturn('')
            ->with($this->equalTo('throws'));

        $method = (new \ReflectionClass(ExceptionStubClass::class))
            ->getMethod('methodA');

        $exceptionMethod = new ExceptionMethod(
            $method, $this->annotationMock
        );

        $this->assertFalse($exceptionMethod->setExceptionClassName(''));
    }

    public function testSetExceptionClassNameExceptionType()
    {
        $this->expectException(MethodGeneratorException::class);

        $this->exceptionMethod->setExceptionClassName(
            [ExceptionStubClass::class]
        );
    }

    public function testSetExceptionClassNameExceptionExist()
    {
        $this->expectException(MethodGeneratorException::class);

        $this->exceptionMethod->setExceptionClassName(' ');
    }

    public function testGenerate()
    {
        $result = "    public function testMethodAException()
    {
        //TODO Implement test for methodA method
        \$this->expectException(ExceptionStubClass::class);
    }

";
        $this->assertEquals($result, $this->exceptionMethod->generate());
    }
}

class ExceptionStubClass
{
    public function methodA(){}
    public function methodB(){}
}