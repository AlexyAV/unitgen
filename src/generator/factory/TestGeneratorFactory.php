<?php

namespace Unitgen\generator\factory;

use Unitgen\generator\GeneratorCompose;
use Unitgen\generator\classGenerator\TestClass;
use Unitgen\annotation\AnnotationParserInterface;
use Unitgen\generator\methodGenerator\test\TestMethod;
use Unitgen\generator\methodGenerator\fixture\SetUpMethod;
use Unitgen\generator\methodGenerator\test\ExceptionMethod;
use Unitgen\generator\methodGenerator\fixture\TearDownMethod;

/**
 * Class TestGeneratorFactory
 *
 * @package Unitgen\generator\factory
 */
class TestGeneratorFactory extends AbstractGeneratorFactory
{
    /**
     * @param string $targetClassName
     *
     * @return TestClass
     */
    public function getClassGenerator($targetClassName)
    {
        return new TestClass($targetClassName);
    }

    /**
     * @param \ReflectionMethod $method
     *
     * @return TestMethod
     */
    public function getMethodGenerator(\ReflectionMethod $method)
    {
        return new TestMethod($method);
    }

    /**
     * @param \ReflectionMethod         $method
     * @param AnnotationParserInterface $annotationParser
     *
     * @return ExceptionMethod
     */
    public function getExceptionMethodGenerator(
        \ReflectionMethod $method, AnnotationParserInterface $annotationParser
    ) {
        return new ExceptionMethod($method, $annotationParser);
    }

    /**
     * @param \ReflectionClass $class
     *
     * @return SetUpMethod
     */
    public function getSetUpMethodGenerator(\ReflectionClass $class)
    {
        return new SetUpMethod($class);
    }

    /**
     * @return TearDownMethod
     */
    public function getTearDownMethodGenerator()
    {
        return new TearDownMethod;
    }

    /**
     * @return GeneratorCompose
     */
    public function getGeneratorCompose()
    {
        return new GeneratorCompose;
    }
}