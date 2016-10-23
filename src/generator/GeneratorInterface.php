<?php

namespace Unitgen\generator;

/**
 * Interface GeneratorInterface
 *
 * @package Unitgen\generator
 */
interface GeneratorInterface
{
    /**
     * @return string Result structure
     */
    public function generate();
}