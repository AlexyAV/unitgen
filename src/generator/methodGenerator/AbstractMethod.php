<?php

namespace Unitgen\generator\methodGenerator;

use Unitgen\generator\GeneratorInterface;

/**
 * Class AbstractMethod
 *
 * @package Unitgen\generator\methodGenerator
 */
abstract class AbstractMethod implements GeneratorInterface
{
    const PUBLIC_MODIFIER = 'public';

    const METHOD_DEFINITION = 'function';

    const TAB_SPACES = 4;

    /**
     * @var string Result test method name
     */
    protected $methodName;

    /**
     * @return string
     */
    protected abstract function getMethodBody();

    /**
     * @return string
     */
    protected abstract function getMethodName();

    /**
     * Generate test method structure.
     *
     * @return string
     */
    public function generate()
    {
        return $this->getTestMethodHeader() . $this->getMethodBody()
        . $this->getTestMethodBodyEnd();
    }

    /**
     * @return string
     */
    protected function getTestMethodHeader()
    {
        return $this->tab() . self::PUBLIC_MODIFIER . " "
        . self::METHOD_DEFINITION . " "
        . $this->getMethodName() . "()\n" . $this->tab() . "{\n";
    }

    /**
     * @return string
     */
    protected function getTestMethodBodyEnd()
    {
        return "\n" . $this->tab() . "}\n\n";
    }

    /**
     * @param int $repeat
     *
     * @return string
     */
    protected function tab($repeat = 1)
    {
        return str_repeat(' ', $repeat * self::TAB_SPACES);
    }
}