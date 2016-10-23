<?php

namespace Unitgen\dispatcher;

/**
 * Interface DispatcherInterface
 *
 * @package Unitgen\dispatcher
 */
interface DispatcherInterface
{
    /**
     * Create single test file form specified source.
     *
     * @return mixed
     */
    public function createTest();
}