<?php

namespace Unitgen\filter\factory;

use Unitgen\filter\ClassFilter;
use Unitgen\filter\MethodFilter;

/**
 * Class AbstractFilterFactory
 *
 * @package Unitgen\filter\factory
 */
abstract class AbstractFilterFactory
{
    /**
     * @param array $classList
     *
     * @return ClassFilter
     */
    abstract public function getClassFilter(array $classList);

    /**
     * @param array $methodList
     *
     * @return MethodFilter
     */
    abstract public function getMethodFilter(array $methodList);
}