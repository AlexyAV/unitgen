<?php

namespace Unitgen\filter\factory;

use Unitgen\filter\ClassFilter;
use Unitgen\filter\MethodFilter;

/**
 * Class FilterFactory
 *
 * @package Unitgen\filter\factory
 */
class FilterFactory extends AbstractFilterFactory
{
    /**
     * @param \ReflectionClass[] $classList
     *
     * @return ClassFilter
     */
    public function getClassFilter(array $classList)
    {
        return new ClassFilter($classList);
    }

    /**
     * @param \ReflectionMethod[] $methodList
     *
     * @return MethodFilter
     */
    public function getMethodFilter(array $methodList)
    {
        return new MethodFilter($methodList);
    }
}