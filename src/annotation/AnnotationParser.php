<?php

namespace Unitgen\annotation;

use Unitgen\annotation\exceptions\AnnotationParserException;

/**
 * Class AnnotationParser
 *
 * @package Unitgen
 */
class AnnotationParser implements AnnotationParserInterface
{
    /**
     * @var string
     */
    private $docBlockSource;

    /**
     * @var array
     */
    private $parsedTagValues = [];

    /**
     * AnnotationParser constructor.
     *
     * @param $docBlockSource
     */
    public function __construct($docBlockSource = null)
    {
        if ($docBlockSource) {
            $this->setDocBlockSource($docBlockSource);
        }
    }

    /**
     * @param string $docBlockSource
     *
     * @return string
     * @throws AnnotationParserException
     */
    public function setDocBlockSource($docBlockSource)
    {
        if (!is_string($docBlockSource)) {
            throw new AnnotationParserException(
                'Doc block source must be a string. '
                . gettype($docBlockSource) . ' given'
            );
        }

        $this->docBlockSource = trim($docBlockSource);

        $this->parseDocBlockSource();

        return $this->docBlockSource;
    }

    /**
     * @return array
     */
    private function parseDocBlockSource()
    {
        $sourceLines = explode("\n", $this->docBlockSource);

        $parsedResults = [];

        $lastTag = null;

        foreach ($sourceLines as $line) {
            if (preg_match('/^\s*\*\/$/', $line)) {
                break;
            }

            if (preg_match('/^\s*\*\s*@(\w+)\s+(\S.*)/i', $line, $result)) {
                if (in_array($result[1], $this->getDocTags())) {
                    $lastTag = $result[1];

                    $parsedResults[$lastTag] = trim($result[2]);
                }
            }

            if (
                $lastTag
                && preg_match('/^\s*\*?\s*(\w+.*)/', $line, $plainText)
            ) {
                $parsedResults[$lastTag] .= ' ' . trim($plainText[1]);
            }

            continue;
        }

        $this->parsedTagValues = $parsedResults;

        return $this->parsedTagValues;
    }

    /**
     * @param string $tag
     *
     * @return string|null
     */
    public function getTagValue($tag)
    {
        if (!is_string($tag)) {
            return null;
        }

        $preparedTag = trim($tag);

        if (array_key_exists($tag, $this->parsedTagValues)) {
            return $this->parsedTagValues[$preparedTag];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getParsedTagValues()
    {
        return $this->parsedTagValues;
    }

    /**
     * @return array
     */
    public function getDocTags()
    {
        return [
            'api',
            'author',
            'category',
            'copyright',
            'deprecated',
            'example',
            'filesource',
            'global',
            'ignore',
            'internal',
            'license',
            'link',
            'method',
            'package',
            'param',
            'property',
            'property-read',
            'property-write',
            'return',
            'see',
            'since',
            'source',
            'subpackage',
            'throws',
            'todo',
            'uses',
            'var',
            'version',
        ];
    }
}