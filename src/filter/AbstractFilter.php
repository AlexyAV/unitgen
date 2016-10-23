<?php

namespace Unitgen\filter;

use Unitgen\filter\exceptions\EntityFilterException;

/**
 * Class AbstractFilter
 *
 * @package Unitgen\filter
 */
abstract class AbstractFilter implements FilterInterface
{
    /**
     * @var array Set of data to be filtered
     */
    protected $sourceData = [];

    /**
     * @var array Name filter list
     */
    protected $nameFilterList = [];

    /**
     * @var array Regexp filter list
     */
    protected $regexpFilterList = [];

    /**
     * @var array List of filters that will be applied
     */
    protected $filterList = [];

    /**
     * Apply filters.
     *
     * @return array
     */
    public function filter()
    {
        $filteredEntities = [];

        foreach ($this->sourceData as $entityReflection) {

            if ($this->performFilters($entityReflection->name)) {
                continue;
            }

            $filteredEntities[] = $entityReflection;
        }

        return $filteredEntities;
    }

    /**
     * @param mixed $entityName
     *
     * @return bool
     */
    protected function performFilters($entityName)
    {
        foreach ($this->filterList as $filterName) {

            if (!method_exists($this, $filterName)) {
                continue;
            }

            if ($this->$filterName($entityName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $nameFilterList
     *
     * @return array
     */
    abstract public function setNameFilterList(array $nameFilterList);

    /**
     * @param array $regexpFilterList
     *
     * @return array
     */
    public function setRegexpFilterList(array $regexpFilterList)
    {
        $validateCallback = function ($regexp) {
            if (!is_string($regexp)) {
                throw new EntityFilterException(
                    'Regular expression for filter must be a string. '
                    . gettype($regexp) . ' given.'
                );
            }

            if ($regexp[0] !== $regexp[strlen($regexp) - 1]) {
                throw new EntityFilterException(
                    'Invalid regular expression for filter ' . $regexp . '.'
                );
            }
        };

        array_map($validateCallback, $regexpFilterList);

        $this->regexpFilterList = $regexpFilterList;

        return $this->regexpFilterList;
    }

    /**
     * Set names of filters that will be applied for entity.
     *
     * @param array $filterList
     *
     * @return array
     */
    public function setFilterList(array $filterList)
    {
        $validateCallback = function ($filterName) {
            if (!is_string($filterName)) {
                throw new EntityFilterException(
                    'Filter name that will be applied must be a string. '
                    . gettype($filterName) . ' given.'
                );
            }
        };

        array_map($validateCallback, $filterList);

        $this->filterList = $filterList;

        return $this->filterList;
    }

    /**
     * Perform filter by name.
     *
     * @param string $entityName
     *
     * @return bool
     */
    protected function nameFilter($entityName)
    {
        return in_array($entityName, $this->nameFilterList);
    }

    /**
     * Perform filter by regexp.
     *
     * @param string $entityName
     *
     * @return bool
     */
    protected function regexpFilter($entityName)
    {
        foreach ($this->regexpFilterList as $regexp) {

            if (preg_match($regexp, $entityName)) {
                return true;
            }
        }

        return false;
    }
}