<?php

namespace Unitgen\generator\methodGenerator\fixture;

/**
 * Class TearDownMethod
 *
 * @package Unitgen\generator\methodGenerator\fixture
 */
class TearDownMethod extends AbstractFixture
{
    const DEFAULT_METHOD_NAME = 'tearDown';

    /**
     * @return string
     */
    protected function getMethodBody()
    {
        return $this->tab(2) . "//TODO Implement tearDown method";
    }

    /**
     * @return string
     */
    protected function getMethodName()
    {
        return self::DEFAULT_METHOD_NAME;
    }
}