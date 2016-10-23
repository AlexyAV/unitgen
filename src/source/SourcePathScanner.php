<?php

namespace Unitgen\source;

use Unitgen\source\exception\SourceException;

/**
 * Class SourcePathScanner
 *
 * @package Unitgen\source
 */
class SourcePathScanner
{
    /**
     * @var string Source files path
     */
    private $sourcePath;

    /**
     * @var bool Whether to scan source path recursively
     */
    private $scanRecursively = true;

    /**
     * @var array List of dirs that will be excluded
     */
    private $excludeDir = [];

    /**
     * @var string
     */
    private static $phpFilesRegexp = '/^.+\.php$/i';

    /**
     * SourcePathHandler constructor.
     *
     * @param string $sourcePath
     */
    public function __construct($sourcePath = null)
    {
        if ($sourcePath) {
            $this->setSourcePath($sourcePath);
        }
    }

    /**
     * Get list of found files in source path.
     *
     * @return \CallbackFilterIterator
     */
    public function fetchTargetFiles()
    {
        $filesIterator = null;

        if ($this->scanRecursively) {
            $filesIterator = $this->getRecursiveFilesIterator();
        } else {
            $filesIterator = $this->getFilesIterator();
        }

        // Get only with .php extension
        $filesIterator = new \RegexIterator(
            $filesIterator,
            self::$phpFilesRegexp,
            \RecursiveRegexIterator::GET_MATCH
        );

        return $this->filterDir($filesIterator);
    }

    /**
     * @return \RecursiveIteratorIterator
     */
    private function getRecursiveFilesIterator()
    {
        return new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->sourcePath),
            \RecursiveIteratorIterator::SELF_FIRST
        );
    }

    private function getFilesIterator()
    {
        return new \FilesystemIterator($this->sourcePath);
    }

    /**
     * Filter found files with exclude list.
     *
     * @param \Iterator $filesIterator
     *
     * @return \CallbackFilterIterator
     */
    private function filterDir(\Iterator $filesIterator)
    {
        $filterCallback = function ($current, $key, $iterator) {

            foreach ($this->excludeDir as $dirPath) {

                $dirPath = realpath(rtrim($dirPath, DIRECTORY_SEPARATOR));

                if (strpos($key, $dirPath) !== false) {
                    return false;
                }
            }

            return true;
        };

        return new \CallbackFilterIterator(
            $filesIterator, $filterCallback
        );
    }

    /**
     * Set list of paths that will be excluded from final result.
     *
     * @param array $excludeDir
     *
     * @return array
     */
    public function setExcludeDir(array $excludeDir)
    {
        $validationCallback = function ($dirPath) {
            if (!is_string($dirPath) || !is_dir($dirPath)) {
                throw new SourceException(
                    'Specified dir to be exclude does not exist.'
                );
            }
        };

        array_map($validationCallback, $excludeDir);

        $this->excludeDir = $excludeDir;

        return $this->excludeDir;
    }

    /**
     * Set path for save generated tests files.
     *
     * @param string $sourcePath
     *
     * @return $this
     * @throws SourceException
     */
    public function setSourcePath($sourcePath)
    {
        if (!is_string($sourcePath)) {
            throw new SourceException(
                'Path to source files must be a string '
                . gettype($sourcePath) . ' given.'
            );
        }

        $preparedSourcePath = trim($sourcePath);

        if (!is_dir($preparedSourcePath)) {
            throw new SourceException(
                'Check that specified source path exist.'
            );
        }

        $this->sourcePath = realpath($preparedSourcePath);

        return $this;
    }

    /**
     * Defines whether to generate tests recursively.
     *
     * @param bool $scanRecursively
     *
     * @return $this
     */
    public function setGenerateTestRecursively($scanRecursively)
    {
        $this->scanRecursively = (bool)$scanRecursively;

        return $this;
    }
}