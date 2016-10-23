<?php

namespace Unitgen;

use Unitgen\config\Config;
use Unitgen\source\SourceFileParser;
use Unitgen\source\SourcePathScanner;
use Unitgen\dispatcher\GeneratorDispatcher;
use Unitgen\filter\factory\AbstractFilterFactory;
use Unitgen\generator\factory\TestGeneratorFactory;

/**
 * Class Controller
 *
 * @package Unitgen
 */
class Controller
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var SourcePathScanner
     */
    private $sourcePathScanner;

    /**
     * @var SourceFileParser
     */
    private $sourceFileParser;

    /**
     * @var AbstractFilterFactory
     */
    private $filterFactory;

    /**
     * @var \ReflectionClass[] List of source classes
     */
    private $targetClassList = [];

    /**
     * @var int Number of files in source path
     */
    private $sourceFilesCount = 0;

    /**
     * @var int Number of generated test classes
     */
    private $targetClassesCount = 0;

    /**
     * Controller constructor.
     *
     * @param Config                $config
     * @param SourcePathScanner     $sourcePathScanner
     * @param SourceFileParser      $sourceFileParser
     * @param AbstractFilterFactory $filterFactory
     */
    public function __construct(
        Config $config,
        SourcePathScanner $sourcePathScanner,
        SourceFileParser $sourceFileParser,
        AbstractFilterFactory $filterFactory
    ) {
        $this->config = $config;

        $this->sourcePathScanner = $sourcePathScanner;

        $this->sourceFileParser = $sourceFileParser;

        $this->filterFactory = $filterFactory;
    }

    /**
     * Start test generation.
     */
    public function runGenerator()
    {
        $this->performSourceFileScan();

        $this->targetClassList = $this->filterClasses(
            $this->config->getClassExcludeData(), $this->targetClassList
        );

        $this->prepareSourceClassData();

        return $this;
    }

    /**
     * Find all files in specified source path and fetch their classes.
     *
     * @return \ReflectionClass[]
     */
    private function performSourceFileScan()
    {
        foreach ($this->config->getSourcePath() as $path => $scanRecursively) {

            // Set source scanner options
            $this->sourcePathScanner->setSourcePath($path)
                ->setGenerateTestRecursively($scanRecursively)
                ->setExcludeDir($this->config->getSourcePathExclude());

            $classList = $this->fetchClassesFromPath(
                $this->sourcePathScanner->fetchTargetFiles()
            );

            $this->targetClassList = array_merge(
                $classList, $this->targetClassList
            );
        }

        return $this->targetClassList;
    }

    /**
     * Run test generation for each found class.
     *
     * @return $this
     */
    private function prepareSourceClassData()
    {
        foreach ($this->targetClassList as $classReflection) {
            $this->createSingleTestFile(
                $classReflection, $this->getMethods($classReflection)
            );
        }

        return $this;
    }

    /**
     * Get method of specified class reflection.
     *
     * @param \ReflectionClass $classReflection
     *
     * @return array|\ReflectionMethod[]
     */
    private function getMethods(\ReflectionClass $classReflection)
    {
        $ownMethods = $classReflection->getMethods();

        // Filter to skip inherited methods
        return array_filter(
            $ownMethods,
            function ($methodReflection) use ($classReflection) {
                /** @var \ReflectionMethod $value */
                return $methodReflection->class == $classReflection->name;
            }
        );
    }

    /**
     * Run generation of single test with specified class and method data.
     *
     * @param \ReflectionClass    $classReflection
     * @param \ReflectionMethod[] $targetMethodList
     */
    private function createSingleTestFile(
        \ReflectionClass $classReflection, $targetMethodList
    ) {
        // Filter class method with client config
        $targetMethodList = $this->filterFactory->getMethodFilter(
            $targetMethodList
        )->filter();

        $generatorDispatcher = new GeneratorDispatcher(
            new TestFileCreator,
            new TestGeneratorFactory,
            $classReflection,
            $targetMethodList,
            $this->config->getSavePath()
        );

        if ($generatorDispatcher->createTest()) {
            $this->targetClassesCount++;
        }
    }

    /**
     * Apply class filter from client config.
     *
     * @param $classExcludeData
     * @param $classList
     *
     * @return array
     */
    private function filterClasses(array $classExcludeData, $classList)
    {
        $classFilter = $this->filterFactory->getClassFilter($classList);

        foreach ($classExcludeData as $excludeParam => $excludeData) {

            $excludeSetMethodName = 'set' . ucfirst($excludeParam);

            if (!method_exists($classFilter, $excludeSetMethodName)) {
                continue;
            }

            $classFilter->$excludeSetMethodName($excludeData);
        }

        return $classFilter->filter();
    }

    /**
     * Get found classes list from all files in path.
     *
     * @param \Iterator $filesIterator
     *
     * @return array
     */
    private function fetchClassesFromPath($filesIterator)
    {
        $classList = [];

        foreach ($filesIterator as $filePath => $iterator) {

            $this->sourceFilesCount++;

            $this->sourceFileParser->setFilePath($filePath);

            $classReflection = $this->sourceFileParser->getReflection();

            if (!$classReflection) {
                continue;
            }

            $classList[] = $classReflection;
        }

        return $classList;
    }

    /**
     * Get number of files found in source path.
     *
     * @return int
     */
    public function getSourceFilesCount()
    {
        return $this->sourceFilesCount;
    }

    /**
     * Get number of generated test classes.
     *
     * @return int
     */
    public function getTargetClassesCount()
    {
        return $this->targetClassesCount;
    }
}