<?php

namespace Unitgen\filter;

use Unitgen\filter\exceptions\ClassFilterException;

/**
 * Class ClassFilter
 *
 * @package Unitgen\filter
 */
class ClassFilter extends AbstractFilter
{
    /**
     * @var array Parent filter list
     */
    protected $parentFilterList = [];

    /**
     * @var array Interface filter list
     */
    protected $interfaceFilterList = [];

    /**
     * @var array List of filters that will be applied
     */
    protected $filterList
        = [
            'nameFilter',
            'regexpFilter',
            'interfaceFilter',
            'parentClassFilter',
            'isInstantiableFilter',
        ];

    /**
     * ClassFilter constructor.
     *
     * @param \ReflectionClass[] $classList
     */
    public function __construct(array $classList)
    {
        $this->setSourceData($classList);
    }

    /**
     * Set class reflection list to be filtered.
     *
     * @param array $sourceData
     *
     * @return array
     * @throws ClassFilterException
     */
    public function setSourceData(array $sourceData)
    {
        $validateCallback = function ($value) {
            if (!($value instanceof \ReflectionClass)) {
                throw new ClassFilterException(
                    'Each class for filter must be an instance'
                    . ' of ReflectionClass'
                );
            }
        };

        array_map($validateCallback, $sourceData);

        $this->sourceData = $sourceData;

        return $this->sourceData;
    }


    /**
     * Set list of classes for parent filter.
     *
     * @param array $parentFilterList
     *
     * @return array
     */
    public function setParent(array $parentFilterList)
    {
        array_map(
            function ($className) {
                $this->checkForClassExist($className);
            },
            $parentFilterList
        );

        $this->parentFilterList = $parentFilterList;

        return $this->parentFilterList;
    }

    /**
     * @param string $className
     *
     * @throws ClassFilterException
     */
    private function checkForClassExist($className)
    {
        if (!is_string($className)) {
            throw new ClassFilterException(
                'Name of class to be filtered must be as string. '
                . gettype($className) . ' given.'
            );
        }

        if (!class_exists($className)) {
            throw new ClassFilterException('Class for filter does not exist.');
        }
    }

    /**
     * Set list of class names filter.
     *
     * @param array $nameFilterList
     *
     * @return array
     */
    public function setNameFilterList(array $nameFilterList)
    {
        array_map(
            function ($className) {
                $this->checkForClassExist($className);
            },
            $nameFilterList
        );

        $this->nameFilterList = $nameFilterList;

        return $this->nameFilterList;
    }

    /**
     * Set list of interfaces to be filtered.
     *
     * @param array $interfaceList
     *
     * @return array
     */
    public function setInterface(array $interfaceList)
    {
        $validateCallback = function ($interfaceName) {

            if (!is_string($interfaceName)) {
                throw new ClassFilterException(
                    'Name of interface to be filtered must be as string. '
                    . gettype($interfaceName) . ' given.'
                );
            }

            if (!interface_exists($interfaceName)) {
                throw new ClassFilterException(
                    'Interface for filter does not exist.'
                );
            }
        };

        array_map($validateCallback, $interfaceList);

        $this->interfaceFilterList = $interfaceList;

        return $this->interfaceFilterList;
    }

    /**
     * Perform filter by parent class.
     *
     * @param string $className
     *
     * @return bool
     */
    protected function parentClassFilter($className)
    {
        $reflectionClass = new \ReflectionClass($className);

        foreach ($this->parentFilterList as $parentClass) {
            if ($reflectionClass->isSubclassOf($parentClass)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Perform filter by class interface.
     *
     * @param string $className
     *
     * @return bool
     */
    protected function interfaceFilter($className)
    {
        $reflectionClass = new \ReflectionClass($className);

        foreach ($this->interfaceFilterList as $interfaceName) {

            if ($reflectionClass->implementsInterface($interfaceName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Filter for only instantiable classes.
     *
     * @param $className
     *
     * @return bool
     */
    protected function isInstantiableFilter($className)
    {
        $reflectionClass = new \ReflectionClass($className);

        return !$reflectionClass->isInstantiable();
    }
}