<?php

namespace Unitgen\generator\methodGenerator\test;

use Unitgen\annotation\AnnotationParserInterface;
use Unitgen\generator\exceptions\MethodGeneratorException;

/**
 * Class ExceptionMethod
 *
 * @package Unitgen\generator\methodGenerator\test
 */
class ExceptionMethod extends AbstractTestMethod
{
    const EXCEPTION_METHOD_NAME_END = 'Exception';

    /**
     * @var AnnotationParserInterface
     */
    private $annotationParser;

    /**
     * @var string
     */
    private $exceptionClassName;

    /**
     * ExceptionMethod constructor.
     *
     * @param \ReflectionMethod         $reflectionMethod
     * @param AnnotationParserInterface $annotationParser
     */
    public function __construct(
        \ReflectionMethod $reflectionMethod,
        AnnotationParserInterface $annotationParser
    ) {
        parent::__construct($reflectionMethod);

        $this->annotationParser = $annotationParser;

        $this->setExceptionClassName(
            $this->fetchExceptionClassFromAnnotation()
        );
    }

    /**
     * Parse source method annotation to get name of exception will be thrown.
     *
     * @return bool|string
     */
    protected function fetchExceptionClassFromAnnotation()
    {
        $exceptionData = $this->annotationParser->getTagValue('throws');

        if (
            !$exceptionData
            || !preg_match(
                '/^((?:\x2f)?[a-z][\w\x2f]*)\s?/i',
                $exceptionData,
                $result
            )
        ) {
            return false;
        }

        return $result[1];
    }

    /**
     * @param string $exceptionClassName
     *
     * @return string
     * @throws MethodGeneratorException
     */
    public function setExceptionClassName($exceptionClassName)
    {
        if (!$exceptionClassName) {
            return false;
        }

        if (!is_string($exceptionClassName)) {
            throw new MethodGeneratorException(
                'Exception class name must be a string. '
                . gettype($exceptionClassName) . ' given'
            );
        }

        $preparedExceptionClassName = trim($exceptionClassName);

        if (!$preparedExceptionClassName) {
            throw new MethodGeneratorException(
                'Exception class name can not be empty.'
            );
        }

        $this->exceptionClassName = $preparedExceptionClassName;

        return $this->exceptionClassName;
    }

    /**
     * @return string
     */
    public function generate()
    {
        return $this->exceptionClassName ? parent::generate() : '';
    }

    /**
     * @return string
     */
    protected function getMethodBody()
    {
        return $this->getToDoMessage() . $this->tab(2)
        . "\$this->expectException($this->exceptionClassName::class);";
    }

    /**
     * @return string
     */
    protected function getMethodName()
    {
        return parent::getMethodName() . self::EXCEPTION_METHOD_NAME_END;
    }
}