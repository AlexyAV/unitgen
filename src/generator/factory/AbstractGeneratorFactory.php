<?php

namespace Unitgen\generator\factory;

use Unitgen\generator\GeneratorCompose;
use Unitgen\annotation\AnnotationParserInterface;
use Unitgen\generator\classGenerator\AbstractClass;
use Unitgen\generator\methodGenerator\fixture\AbstractFixture;
use Unitgen\generator\methodGenerator\test\AbstractTestMethod;

/**
 * Class AbstractGeneratorFactory
 *
 * @package Unitgen\generator\factory
 */
abstract class AbstractGeneratorFactory
{
    /**
     * @param string $targetClassName
     *
     * @return AbstractClass
     */
    abstract public function getClassGenerator($targetClassName);

    /**
     * @param \ReflectionMethod $method
     *
     * @return AbstractTestMethod
     */
    abstract public function getMethodGenerator(\ReflectionMethod $method);

    /**
     * @param \ReflectionMethod         $method
     * @param AnnotationParserInterface $annotationParser
     *
     * @return AbstractTestMethod
     */
    abstract public function getExceptionMethodGenerator(
        \ReflectionMethod $method, AnnotationParserInterface $annotationParser
    );

    /**
     * @param \ReflectionClass $class
     *
     * @return AbstractFixture
     */
    abstract public function getSetUpMethodGenerator(\ReflectionClass $class);

    /**
     * @return AbstractFixture
     */
    abstract public function getTearDownMethodGenerator();

    /**
     * @return GeneratorCompose
     */
    abstract public function getGeneratorCompose();
}