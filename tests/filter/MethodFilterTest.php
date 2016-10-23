<?php

use PHPUnit\Framework\TestCase;
use Unitgen\filter\exceptions\EntityFilterException;
use Unitgen\filter\exceptions\MethodFilterException;
use Unitgen\filter\MethodFilter;

class MethodFilterTest extends TestCase
{
    /**
     * @var MethodFilter
     */
    private $methodFilter;

    public function setUp()
    {
        $methods = (new \ReflectionClass(StubTestClass::class))->getMethods();

        $this->methodFilter = new MethodFilter($methods);
    }

    public function testSetNameFilterList()
    {
        $methods = ['filterA', 'filterB'];

        $this->assertEquals(
            $methods, $this->methodFilter->setNameFilterList($methods)
        );
    }

    public function testSetNameFilterListException()
    {
        $this->expectException(MethodFilterException::class);

        $this->methodFilter->setNameFilterList([1,2,3]);
    }

    public function testSetFilterList()
    {
        $filterList = ['filterA', 'filterB'];

        $this->assertEquals(
            $filterList, $this->methodFilter->setFilterList($filterList)
        );
    }

    public function testSetFilterListException()
    {
        $this->expectException(EntityFilterException::class);

        $filterList = ['filterA', ['filterB']];

        $this->methodFilter->setFilterList($filterList);
    }

    public function testSetModifierFilterList()
    {
        $this->assertEquals(
            [
                MethodFilter::IS_PUBLIC,
                MethodFilter::IS_PROTECTED,
                MethodFilter::IS_PRIVATE,
            ],
            $this->methodFilter->setModifierFilterList(
                [MethodFilter::IS_PROTECTED, MethodFilter::IS_PRIVATE]
            )
        );
    }

    public function testSetModifierFilterListException()
    {
        $this->expectException(MethodFilterException::class);

        $this->methodFilter->setModifierFilterList(
            [MethodFilter::IS_PROTECTED, [MethodFilter::IS_PRIVATE]]
        );
    }

    public function testSetRegexpFilterList()
    {
        $regexpList = ['/^__.*/', '/^get.*/'];

        $this->assertEquals(
            $regexpList,
            $this->methodFilter->setRegexpFilterList($regexpList)
        );
    }

    public function testSetRegexpFilterListExceptionType()
    {
        $this->expectException(EntityFilterException::class);

        $regexpList = [['/^__.*/']];

        $this->methodFilter->setRegexpFilterList($regexpList);
    }

    public function testSetRegexpFilterListExceptionFormat()
    {
        $this->expectException(EntityFilterException::class);

        $regexpList = ['/^__.*'];

        $this->methodFilter->setRegexpFilterList($regexpList);
    }

    public function testFilter()
    {
        $this->methodFilter->setNameFilterList(['methodA', 'methodD']);
        $this->methodFilter->setRegexpFilterList(['/^__.*/']);
        // will be skipped
        $this->methodFilter->setModifierFilterList(['InvalidModifier']);
        $this->methodFilter->setFilterList(
            ['nameFilter', 'regexpFilter', 'invalidFilter']
        );

        $this->assertEquals(
            [
                new \ReflectionMethod(StubTestClass::class, 'methodB'),
                new \ReflectionMethod(StubTestClass::class, 'methodC')
            ],
            $this->methodFilter->filter()
        );
    }

    public function testSetSourceData()
    {
        $methods = (new \ReflectionClass(StubTestClass::class))->getMethods();

        $this->assertEquals(
            $methods, $this->methodFilter->setSourceData($methods)
        );
    }

    public function testSetSourceDataException()
    {
        $this->expectException(MethodFilterException::class);

        $methods = get_class_methods(StubTestClass::class);

        $this->methodFilter->setSourceData($methods);
    }
}

class StubTestClass {
    public function __construct(){}
    public function methodA(){}
    public function methodB(){}
    public function methodC(){}
    public function methodD(){}
    private function methodE(){}
}