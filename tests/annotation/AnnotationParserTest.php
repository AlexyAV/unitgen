<?php

namespace Unitgen\Test\annotation;

use PHPUnit\Framework\TestCase;
use Unitgen\annotation\AnnotationParser;
use Unitgen\annotation\exceptions\AnnotationParserException;

class AnnotationParserTest extends TestCase
{
    /**
     * @var AnnotationParser
     */
    private $annotationParser;

    public function setUp()
    {
        $docBlock = "/**
         * @param string \$docBlockSource
         *
         * @return string
         * @throws AnnotationParserException
         */";

        $this->annotationParser = new AnnotationParser($docBlock);
    }

    public function testSetDocBlockSource()
    {
        $docBlock = "/**
         * @param string \$docBlockSource
         *
         * @return string
         * @throws AnnotationParserException
         */";

        $this->annotationParser->setDocBlockSource($docBlock);

        $this->assertEquals(
            $this->annotationParser->setDocBlockSource($docBlock),
            $docBlock
        );
    }

    public function setDocBlockSourceExceptionProvider()
    {
        return [
            [new \stdClass()],
            [[]],
            [1],
            [12.4]
        ];
    }

    public function testSetDocBlockSourceException()
    {
        $this->expectException(AnnotationParserException::class);

        $this->annotationParser->setDocBlockSource([]);
    }

    public function testGetTagValue()
    {
        $docBlock = '/**
         * @param string $docBlockSource
         *
         * @return string
         * @throws AnnotationParserException
         */';

        $this->annotationParser->setDocBlockSource($docBlock);

        $tags = [
            'param' => 'string $docBlockSource',
            'return' => 'string',
            'throws' => 'AnnotationParserException'
        ];

        foreach ($tags as $tag => $value) {
            $this->assertEquals(
                $this->annotationParser->getTagValue($tag), $value
            );
        }

        $this->assertNull($this->annotationParser->getTagValue('invalidTag'));

        $this->assertNull($this->annotationParser->getTagValue(['invalidTag']));
    }

    public function testGetParsedTagValues()
    {
        $docBlock = '/**
         * @param string $docBlockSource
         *
         * @return string Some explanation
         * on separate lines
         * @throws AnnotationParserException
         */';

        $this->annotationParser->setDocBlockSource($docBlock);

        $tags = [
            'param' => 'string $docBlockSource',
            'return' => 'string Some explanation on separate lines',
            'throws' => 'AnnotationParserException'
        ];

        $this->assertEquals(
            $this->annotationParser->getParsedTagValues(), $tags
        );
    }

    public function testGetDocTags()
    {
        $docTags = $this->annotationParser->getDocTags();

        $this->assertTrue(in_array('throws', $docTags));
        $this->assertTrue(in_array('param', $docTags));
        $this->assertTrue(in_array('version', $docTags));
    }

}