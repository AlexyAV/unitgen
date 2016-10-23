<?php

namespace Unitgen\generator\methodGenerator\fixture;

/**
 * Class SetUpMethod
 *
 * @package Unitgen\generator\methodGenerator\fixture
 */
class SetUpMethod extends AbstractFixture
{
    const DEFAULT_METHOD_NAME = 'setUp';

    /**
     * SetUpMethod constructor.
     *
     * @param \ReflectionClass $targetClassReflection
     */
    public function __construct(\ReflectionClass $targetClassReflection)
    {
        parent::__construct($targetClassReflection);
    }

    /**
     * @return string
     */
    protected function getMethodBody()
    {
        return $this->tab(2) . "\$this->"
        . lcfirst($this->targetClassReflection->getShortName()) . " = new \\"
        . $this->targetClassReflection->getName() . "();";
    }

    /**
     * @return string
     */
    protected function getMethodName()
    {
        return self::DEFAULT_METHOD_NAME;
    }
}