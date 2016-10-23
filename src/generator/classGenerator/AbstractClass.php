<?php

namespace Unitgen\generator\classGenerator;

use Unitgen\generator\GeneratorInterface;
use Unitgen\generator\exceptions\ClassGeneratorException;

/**
 * Class AbstractClass
 *
 * @package Unitgen\generator\classGenerator
 */
abstract class AbstractClass implements GeneratorInterface
{
    const CLASS_DEFINITION = 'class';

    const EXTENDS_DEFINITION = 'extends';

    const IMPLEMENT_DEFINITION = 'implements';

    /**
     * @var string Default parent class name
     */
    protected $defaultParentClass = '';

    /**
     * @var string Source class name
     */
    protected $targetClassName;

    /**
     * @var string|null Current parent class
     */
    protected $parentClass;

    /**
     * @var array Current interfaces list
     */
    protected $interface = [];

    /**
     * ClassGenerator constructor.
     *
     * @param string      $targetClassName
     * @param string|null $parentClass
     * @param string|null $interface
     */
    public function __construct(
        $targetClassName, $parentClass = null, $interface = null
    ) {
        $this->setTargetClassName($targetClassName);

        if ($parentClass) {
            $this->setParentClass($parentClass);
        }

        if ($interface) {
            $this->setInterface($interface);
        }
    }

    public function setTargetClassName($targetClassName)
    {
        $this->validateClassName($targetClassName);

        $this->targetClassName = $targetClassName;
    }

    /**
     * Set name of test class parent.
     *
     * @param string $parentClass
     *
     * @return string
     * @throws ClassGeneratorException
     */
    public function setParentClass($parentClass)
    {
        $this->validateClassName($parentClass);

        if (!class_exists($parentClass)) {
            throw new ClassGeneratorException(
                'Specified target parent class does not exist.'
            );
        }

        $this->parentClass = $parentClass;

        return $this->parentClass;
    }

    /**
     * Set name of test class interface.
     *
     * @param string|array $interface
     *
     * @return string
     * @throws ClassGeneratorException
     */
    public function setInterface($interface)
    {
        if (!is_string($interface) && !is_array($interface)) {
            throw new ClassGeneratorException(
                'Class interface must be specified as string or array of '
                . 'strings. '. gettype($interface) . ' given.'
            );
        }

        $interfaceList = [];

        if (is_string($interface)) {
            $interfaceList[] = $interface;
        }

        // Convert array of interfaces to string
        if (is_array($interface)) {
            $interfaceList = $interface;
        }

        // Validate interface list
        $interfaceList = array_map(
            function($interfaceName) {
                $this->validateInterface($interfaceName);

                return $interfaceName;
            },
            $interfaceList
        );

        $this->interface = $interfaceList;

        return $this->interface;
    }

    /**
     * Validate interface name for type and existing.
     *
     * @param string $interface
     *
     * @return bool
     * @throws ClassGeneratorException
     */
    protected function validateInterface($interface)
    {
        if (!is_string($interface)) {
            throw new ClassGeneratorException(
                'Interface name must be a string. '
                . gettype($interface) . ' given'
            );
        }

        if (!interface_exists($interface)) {
            throw new ClassGeneratorException(
                'Specified interface does not exist.'
            );
        }
    }

    /**
     * @param string $className
     *
     * @return bool
     * @throws ClassGeneratorException
     */
    private function validateClassName($className)
    {
        if (!is_string($className)) {
            throw new ClassGeneratorException(
                'Class name must be as string. '
                . gettype($className) . ' given.'
            );
        }

        if (
            !preg_match(
                '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff\x5c]*$/', $className)
        ) {
            throw new ClassGeneratorException(
                'Incorrect class name format - ' . $className
            );
        }

        return true;
    }

    /**
     * Generate test class structure.
     *
     * @return string Generated structure
     */
    public function generate()
    {
        return $this->getNamespaceList()
        . self::CLASS_DEFINITION . " " . $this->getTestClassName() . " "
        . $this->getTestClassParent()
        . $this->getTestClassInterface() . "\n{\n";
    }

    /**
     * Generate list of using namespaces
     *
     * @return string
     */
    protected function getNamespaceList()
    {
        $namespaceList = [];

        // Get namespace of parent class
        $parentClass = $this->parentClass?: $this->defaultParentClass;

        if ($parentClass) {
            $parentReflection = new \ReflectionClass($parentClass);

            if ($parentReflection->getNamespaceName()) {
                $namespaceList[] = $parentReflection->getName();
            }
        }

        // Get namespaces of interfaces
        if ($this->interface) {
            foreach ($this->interface as $interfaceName) {

                $interfaceReflection = new \ReflectionClass($interfaceName);

                if ($interfaceReflection->getNamespaceName()) {
                    $namespaceList[] = $interfaceReflection->getName();
                }
            }
        }

        return "use " . implode(";\nuse ", $namespaceList) . ";\n\n";
    }

    /**
     * @return string
     */
    abstract protected function getTestClassName();

    /**
     * @return string
     */
    protected function getTestClassParent()
    {
        $parentClass = $this->parentClass?: $this->defaultParentClass;

        return  self::EXTENDS_DEFINITION . ' ' . (new \ReflectionClass
        ($parentClass))->getShortName();
    }

    /**
     * @return string
     */
    protected function getTestClassInterface()
    {
        if (!$this->interface) {
            return '';
        }

        $interfaceShortNames = [];

        foreach ($this->interface as $interfaceName) {
            $interfaceShortNames[] = (new \ReflectionClass($interfaceName))
                ->getShortName();
        }

        return ' ' . self::IMPLEMENT_DEFINITION . ' ' . implode(', ',
            $interfaceShortNames);
    }
}