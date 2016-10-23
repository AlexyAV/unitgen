<?php

namespace Unitgen\generator\methodGenerator\test;

use Unitgen\generator\methodGenerator\AbstractMethod;
use Unitgen\generator\exceptions\MethodGeneratorException;

/**
 * Class AbstractTestMethod
 *
 * @package Unitgen\generator\methodGenerator\test
 */
abstract class AbstractTestMethod extends AbstractMethod
{
    const TEST_METHOD_PREFIX = 'test';

    /**
     * @var string
     */
    protected $methodName;

    /**
     * AbstractTestMethod constructor.
     *
     * @param \ReflectionMethod $reflectionMethod
     */
    public function __construct(\ReflectionMethod $reflectionMethod)
    {
        $this->setTargetMethodName($reflectionMethod);
    }

    /**
     * @param \ReflectionMethod $reflectionMethod
     *
     * @return string
     */
    public function setTargetMethodName(\ReflectionMethod $reflectionMethod)
    {
        $this->methodName = $reflectionMethod->name;

        return $this->methodName;
    }

    /**
     * @return string
     */
    protected function getMethodName()
    {
        return static::TEST_METHOD_PREFIX . ucfirst($this->methodName);
    }

    /**
     * @return string
     */
    protected function getToDoMessage()
    {
        return $this->tab(2)
        . "//TODO Implement test for $this->methodName method\n";
    }
}