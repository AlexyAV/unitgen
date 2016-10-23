<?php

use PHPUnit\Framework\TestCase;
use Unitgen\filter\ClassFilter;
use Unitgen\filter\factory\FilterFactory;
use Unitgen\filter\MethodFilter;

class FilterFactoryTest extends TestCase
{
    /**
     * @var FilterFactory
     */
    public $filterFactory;

    public function setUp()
    {
        $this->filterFactory = new FilterFactory();
    }

    public function testGetClassFilter()
    {
        $this->assertInstanceOf(
            ClassFilter::class,
            $this->filterFactory->getClassFilter(
                [new ReflectionClass(stdClass::class)]
            )
        );
    }

    public function testGetMethodFilter()
    {
        $this->assertInstanceOf(
            MethodFilter::class,
            $this->filterFactory->getMethodFilter(
                (new ReflectionClass(\Exception::class))->getMethods()
            )
        );
    }
}