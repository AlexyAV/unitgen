<?php

namespace Unitgen\generator\classGenerator;

use PHPUnit\Framework\TestCase;

/**
 * Class TestClass
 *
 * @package Unitgen\generator\classGenerator
 */
class TestClass extends AbstractClass
{
    const TEST_CLASS_NAME_END = 'Test';

    /**
     * @var string Default parent class name
     */
    protected $defaultParentClass = TestCase::class;

    /**
     * @return string
     */
    protected function getTestClassName()
    {
        return $this->targetClassName . self::TEST_CLASS_NAME_END;
    }
}