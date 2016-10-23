<?php

namespace Unitgen\filter;

use Unitgen\filter\exceptions\MethodFilterException;

/**
 * Class MethodFilter
 *
 * @package Unitgen\filter
 */
class MethodFilter extends AbstractFilter
{
    const IS_PUBLIC = 'isPublic';

    const IS_STATIC = 'isStatic';

    const IS_PRIVATE = 'isPrivate';

    const IS_PROTECTED = 'isProtected';

    /**
     * @var array List of method modifiers that will pass filter
     */
    protected $modifierFilterList
        = [
            self::IS_PUBLIC
        ];

    /**
     * @var array Method name regexp filter list
     */
    protected $regexpFilterList
        = [
            '/^__.*/'
        ];

    /**
     * @var array List of filters that will be applied
     */
    protected $filterList
        = [
            'nameFilter',
            'regexpFilter',
        ];

    /**
     * MethodFilter constructor.
     *
     * @param \ReflectionMethod[] $methodList
     */
    public function __construct(array $methodList)
    {
        $this->setSourceData($methodList);
    }

    /**
     * Set method name filter list.
     *
     * @param array $nameFilterList
     *
     * @return array
     * @throws MethodFilterException
     */
    public function setNameFilterList(array $nameFilterList)
    {
        $validateCallback = function ($methodName) {
            if (!is_string($methodName)) {
                throw new MethodFilterException(
                    'Name of methods to be filtered must be as string. '
                    . gettype($methodName) . ' given.'
                );
            }
        };

        array_map($validateCallback, $nameFilterList);

        $this->nameFilterList = $nameFilterList;

        return $this->nameFilterList;
    }

    /**
     * @param array $modifierList
     *
     * @return array
     * @throws MethodFilterException
     */
    public function setModifierFilterList(array $modifierList)
    {
        $validateCallback = function ($modifierName) {
            if (!is_string($modifierName)) {
                throw new MethodFilterException(
                    'Method modifier must be as string. '
                    . gettype($modifierName) . ' given.'
                );
            }
        };

        array_map($validateCallback, $modifierList);

        $this->modifierFilterList = array_merge(
            $this->modifierFilterList, $modifierList
        );

        return $this->modifierFilterList;
    }

    /**
     * Set method modifier filter list.
     *
     * @param \ReflectionMethod $methodReflection
     *
     * @return bool
     */
    protected function modifierFilter(\ReflectionMethod $methodReflection)
    {
        foreach ($this->modifierFilterList as $modifier) {
            if (!method_exists($methodReflection, $modifier)) {
                continue;
            }

            // Apply reflection modifier check
            if (!$methodReflection->$modifier()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Apply filters.
     *
     * @return array
     */
    public function filter()
    {
        $filteredMethodList = [];

        $tempFilteredMethodList = parent::filter();

        // Apply method modifiers filter
        foreach ($tempFilteredMethodList as $methodReflection) {

            if ($this->modifierFilter($methodReflection)) {
                continue;
            }

            $filteredMethodList[] = $methodReflection;
        }

        return $filteredMethodList;
    }

    /**
     * Set list of methods to be filtered.
     *
     * @param array $sourceData
     *
     * @return array|\ReflectionMethod[]
     * @throws MethodFilterException
     */
    public function setSourceData(array $sourceData)
    {
        $validateCallback = function ($value) {
            if (!($value instanceof \ReflectionMethod)) {
                throw new MethodFilterException(
                    'Each method for filter must be an instance'
                    . ' of ReflectionMethod'
                );
            }
        };

        array_map($validateCallback, $sourceData);

        $this->sourceData = $sourceData;

        return $this->sourceData;
    }
}