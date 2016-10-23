<?php

namespace Unitgen\generator\methodGenerator\test;

/**
 * Class TestMethod
 *
 * @package Unitgen\generator\methodGenerator\test
 */
class TestMethod extends AbstractTestMethod
{
    /**
     * @return string
     */
    protected function getMethodBody()
    {
        return $this->getToDoMessage()
        . $this->tab(2) . "\$this->assertTrue(false);";
    }
}