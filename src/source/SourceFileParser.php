<?php

namespace Unitgen\source;

use Unitgen\source\exception\SourceException;

/**
 * Class SourceFileParser
 *
 * @package Unitgen\source
 */
class SourceFileParser
{
    /**
     * @var string Path to source file
     */
    private $filePath;

    /**
     * @var string Full name of parsed class
     */
    private $fullClassName;

    /**
     * SourceFileParser constructor.
     *
     * @param string|null $filePath
     */
    public function __construct($filePath = null)
    {
        if ($filePath) {
            $this->setFilePath($filePath);
        }
    }

    /**
     * @return string
     */
    public function getFullClassName()
    {
        return $this->fullClassName;
    }

    /**
     * @param string $filePath
     *
     * @return string
     * @throws SourceException
     */
    public function setFilePath($filePath)
    {
        $this->filePath = null;

        if (!is_string($filePath)) {
            throw new SourceException(
                'Path to source file must be a string '
                . gettype($filePath) . ' given.'
            );
        }

        if (!file_exists($filePath) || !is_file($filePath)) {
            throw new SourceException(
                'Specified source path does not exist.'
            );
        }

        $this->filePath = realpath($filePath);

        $this->initFullClassName();

        return $this->filePath;
    }

    /**
     * Fetch class name from source file path. It does not check for actual
     * class existing on this step.
     *
     * @return bool
     */
    private function getClassNameFromFileName()
    {
        $parts = explode(DIRECTORY_SEPARATOR, $this->filePath);

        $fileName = array_pop($parts);

        if (
        preg_match(
            '/^([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)\.[a-z]{3}$/',
            $fileName,
            $match
        )
        ) {
            return $match[1];
        }

        return false;
    }

    /**
     * Get full class name including namespace.
     *
     * @return null|string
     */
    private function initFullClassName()
    {
        $fullClassName = null;

        $namespace = $this->getClassNamespace();

        $className = $this->getClassNameFromFileName();

        if ($namespace && $className) {
            $fullClassName = $namespace . '\\' . $className;
        }

        $this->fullClassName = $fullClassName;

        return $this->fullClassName;
    }

    /**
     * @return null|\ReflectionClass
     */
    public function getReflection()
    {
        if (!$this->fullClassName || !class_exists($this->fullClassName)) {
            return null;
        }

        return new \ReflectionClass($this->fullClassName);
    }

    /**
     * Get namespace from source file with parsing.
     *
     * @return bool
     */
    private function getClassNamespace()
    {
        $fileResource = fopen($this->filePath, 'r');

        while (!feof($fileResource)) {
            $line = trim(fgets($fileResource));

            if (!$line || preg_match('/^<\?php|\/\*\*|\s?\*.*/', $line)) {
                continue;
            }

            if (
            preg_match(
                '#^(?!\d)\s*?namespace\s+([\w|\x5c]+);#', $line, $match
            )
            ) {
                return $match[1];
            }

            break;
        }

        return false;
    }
}