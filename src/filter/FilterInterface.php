<?php

namespace Unitgen\filter;

/**
 * Interface FilterInterface
 *
 * @package Unitgen\filter
 */
interface FilterInterface
{
    /**
     * @param array $sourceData Raw data
     *
     * @return array
     */
    public function setSourceData(array $sourceData);

    /**
     * @return array Filtered entities
     */
    public function filter();
}