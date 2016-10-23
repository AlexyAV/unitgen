<?php

namespace Unitgen\generator\methodGenerator\fixture;

use Unitgen\generator\methodGenerator\AbstractMethod;

/**
 * Class AbstractFixture
 *
 * @package Unitgen\generator\methodGenerator\fixture
 */
abstract class AbstractFixture extends AbstractMethod
{
    /**
     * @var \ReflectionClass
     */
    protected $targetClassReflection;

    /**
     * AbstractFixture constructor.
     *
     * @param \ReflectionClass $targetClassReflection
     */
    public function __construct(\ReflectionClass $targetClassReflection = null)
    {
        $this->targetClassReflection = $targetClassReflection;
    }
}