<?php

namespace Unitgen\annotation;

/**
 * Interface AnnotationParserInterface
 *
 * @package Unitgen\annotation
 */
interface AnnotationParserInterface
{
    /**
     * @param string $docBlockSource
     *
     * @return mixed
     */
    public function setDocBlockSource($docBlockSource);

    /**
     * @param string $tag
     *
     * @return string
     */
    public function getTagValue($tag);

    /**
     * @return array
     */
    public function getParsedTagValues();
}