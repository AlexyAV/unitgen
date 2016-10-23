<?php

use PHPUnit\Framework\TestCase;
use Unitgen\filter\ClassFilter;
use Unitgen\filter\exceptions\ClassFilterException;

class ClassFilterTest extends TestCase
{
    /**
     * @var ClassFilter
     */
    private $classFilter;

    public function setUp()
    {
        $this->classFilter = new ClassFilter(
            [
                new ReflectionClass(stdClass::class)
            ]
        );
    }

    public function testSetSourceData()
    {
        $sourceData = [new ReflectionClass(ClassFilter::class)];

        $this->assertEquals(
            $sourceData, $this->classFilter->setSourceData($sourceData)
        );
    }

    public function testSetSourceDataException()
    {
        $this->expectException(ClassFilterException::class);

        $sourceData = [new stdClass()];

        $this->classFilter->setSourceData($sourceData);
    }

    public function testSetParent()
    {
        $parentClass = [stdClass::class];

        $this->assertEquals(
            $parentClass, $this->classFilter->setParent($parentClass)
        );
    }

    public function testSetParentExceptionType()
    {
        $this->expectException(ClassFilterException::class);

        $parentClass = [['InvalidClassName']];

        $this->assertEquals(
            $parentClass, $this->classFilter->setParent($parentClass)
        );
    }

    public function testSetParentExceptionExist()
    {
        $this->expectException(ClassFilterException::class);

        $parentClass = ['InvalidClassName'];

        $this->assertEquals(
            $parentClass, $this->classFilter->setParent($parentClass)
        );
    }

    public function testDetNameFilterList()
    {
        $className = [stdClass::class];

        $this->assertEquals(
            $className, $this->classFilter->setNameFilterList($className)
        );
    }

    public function testSetNameExceptionType()
    {
        $this->expectException(ClassFilterException::class);

        $className = [['InvalidClassName']];

        $this->assertEquals(
            $className, $this->classFilter->setNameFilterList($className)
        );
    }

    public function testSetNameExceptionExist()
    {
        $this->expectException(ClassFilterException::class);

        $className = ['InvalidClassName'];

        $this->assertEquals(
            $className, $this->classFilter->setNameFilterList($className)
        );
    }

    public function testSetInterface()
    {
        $interface = [\Traversable::class];

        $this->assertEquals(
            $interface, $this->classFilter->setInterface($interface)
        );
    }

    public function testSetInterfaceExceptionType()
    {
        $this->expectException(ClassFilterException::class);

        $interface = [[\Traversable::class]];

        $this->assertEquals(
            $interface, $this->classFilter->setInterface($interface)
        );
    }

    public function testSetInterfaceExceptionExist()
    {
        $this->expectException(ClassFilterException::class);

        $interface = ['InvalidInterface'];

        $this->assertEquals(
            $interface, $this->classFilter->setInterface($interface)
        );
    }

    public function testFilter()
    {
        $sourceData = [
            new ReflectionClass(ClassFilter::class),
            new ReflectionClass(TestClass::class),
            new ReflectionClass(AnotherTestClass::class),
            new ReflectionClass(\stdClass::class),
        ];

        $this->classFilter->setSourceData($sourceData);

        $this->classFilter->setParent([Exception::class]);

        $this->classFilter->setNameFilterList([ClassFilter::class]);

        $this->classFilter->setInterface([StubTestInterface::class]);

        $this->assertEquals(
            [new ReflectionClass(new \stdClass)],
            $this->classFilter->filter()
        );
    }
}

interface StubTestInterface {}

class TestClass extends \Exception {}
class AnotherTestClass implements StubTestInterface {}