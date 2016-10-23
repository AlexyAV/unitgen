<?php

namespace testNameSpace;

use PHPUnit\Framework\TestCase;
use Unitgen\generator\classGenerator\TestClass;
use Unitgen\generator\exceptions\ClassGeneratorException;

class TestClassTest extends TestCase
{
    /**
     * @var TestClass
     */
    private $classGenerator;

    public function setUp()
    {
        $this->classGenerator = new TestClass(\stdClass::class);
    }

    public function testSetTargetClassNameExceptionType()
    {
        $this->expectException(ClassGeneratorException::class);

        $this->classGenerator->setParentClass([\Exception::class]);
    }

    public function testSetTargetClassNameExceptionFormat()
    {
        $this->expectException(ClassGeneratorException::class);

        $this->classGenerator->setParentClass('1InvalidClass');
    }

    public function testSetParentClass()
    {
        $this->assertEquals(
            \Exception::class,
            $this->classGenerator->setParentClass(\Exception::class)
        );
    }

    public function testSetParentClassExceptionType()
    {
        $this->expectException(ClassGeneratorException::class);

        $this->classGenerator->setParentClass([\Exception::class]);
    }

    public function testSetParentClassExceptionExist()
    {
        $this->expectException(ClassGeneratorException::class);

        $this->classGenerator->setParentClass('InvalidClass');
    }

    public function testSetInterface()
    {
        $this->assertEquals(
            [StubInterfaceA::class],
            $this->classGenerator->setInterface(StubInterfaceA::class)
        );
    }

    public function testSetInterfaceArray()
    {
        $this->assertEquals(
            [StubInterfaceA::class, StubInterfaceB::class],
            $this->classGenerator->setInterface(
                [StubInterfaceA::class, StubInterfaceB::class]
            )
        );
    }

    public function testSetInterfaceExceptionType()
    {
        $this->expectException(ClassGeneratorException::class);

        $this->classGenerator->setInterface(new \stdClass);
    }

    public function testSetInterfaceArrayExceptionType()
    {
        $this->expectException(ClassGeneratorException::class);

        $this->classGenerator->setInterface([new \stdClass]);
    }

    public function testSetInterfaceArrayExceptionExist()
    {
        $this->expectException(ClassGeneratorException::class);

        $this->classGenerator->setInterface(['InvalidClass']);
    }

    public function testGenerate()
    {

        $classGenerator = new TestClass(
            (new \ReflectionClass(\stdClass::class))->getShortName(),
            StubClass::class,
            [StubInterfaceA::class, StubInterfaceB::class]
        );

        $result = "use testNameSpace\\StubClass;
use testNameSpace\\StubInterfaceA;
use testNameSpace\\StubInterfaceB;

class stdClassTest extends StubClass implements StubInterfaceA, StubInterfaceB
{\n";
        $this->assertEquals($result, $classGenerator->generate());
    }

    public function testGenerateClassOnly()
    {

        $classGenerator = new TestClass(
            \stdClass::class
        );

        $result = "use PHPUnit\\Framework\\TestCase;

class stdClassTest extends TestCase
{\n";
        $this->assertEquals($result, $classGenerator->generate());
    }
}

interface StubInterfaceA {};
interface StubInterfaceB {};
class StubClass {};