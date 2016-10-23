<?php

namespace Unitgen\dispatcher;

use Unitgen\TestFileCreator;
use Unitgen\generator\GeneratorCompose;
use Unitgen\annotation\AnnotationParser;
use Unitgen\dispatcher\exceptions\DispatcherException;
use Unitgen\generator\factory\AbstractGeneratorFactory;

class GeneratorDispatcher implements DispatcherInterface
{
    /**
     * @var string Path to test destination
     */
    private $testPath;

    /**
     * @var TestFileCreator
     */
    private $testFileCreator;

    /**
     * @var AbstractGeneratorFactory
     */
    private $generatorFactory;

    /**
     * @var \ReflectionClass
     */
    private $targetClass;

    /**
     * @var \ReflectionMethod[]
     */
    private $targetMethodList;

    /**
     * Dispatcher constructor.
     *
     * @param TestFileCreator          $testFileCreator
     * @param AbstractGeneratorFactory $generatorFactory
     * @param \ReflectionClass         $targetClass
     * @param \ReflectionMethod[]      $targetMethodList
     * @param string                   $testPath
     */
    public function __construct(
        TestFileCreator $testFileCreator,
        AbstractGeneratorFactory $generatorFactory,
        \ReflectionClass $targetClass,
        $targetMethodList,
        $testPath
    ) {
        $this->testFileCreator = $testFileCreator;

        $this->generatorFactory = $generatorFactory;

        $this->targetClass = $targetClass;

        $this->targetMethodList = $targetMethodList;

        $this->setTestPath($testPath);
    }

    /**
     * Set path for save generated tests files.
     *
     * @param string $testPath
     *
     * @return string
     * @throws \Exception
     */
    public function setTestPath($testPath)
    {
        if (!is_string($testPath)) {
            throw new DispatcherException(
                'Path to tests directory must be a string '
                . gettype($testPath) . ' given.'
            );
        }

        $preparedTestPath = trim($testPath);

        if (!is_dir($preparedTestPath) || !is_writable($preparedTestPath)) {
            throw new DispatcherException(
                'Check that specified directory for test exists and has 
                corresponding permissions.'
            );
        }

        $this->testPath = realpath($preparedTestPath);

        return $this->testPath;
    }

    /**
     * @param string $sourcePath
     *
     * @return string
     */
    private function generateTestPath($sourcePath)
    {
        $testPathParts = explode(DIRECTORY_SEPARATOR, $this->testPath);

        $sourcePathParts = explode(DIRECTORY_SEPARATOR, $sourcePath);

        $common = implode(
            DIRECTORY_SEPARATOR,
            array_intersect_assoc($testPathParts, $sourcePathParts)
        );

        return dirname(
            $this->testPath . str_replace($common, '', $sourcePath)
        );
    }

    /**
     * Create new test file by specified source data.
     *
     * @return int
     */
    public function createTest()
    {
        $sourceGenerator = $this->createCommonTestSource($this->targetClass);

        return $this->saveTestFileSource(
            $this->targetClass,
            $this->targetClass->getFileName(),
            $sourceGenerator->generate()
        );
    }

    /**
     * @param \ReflectionClass $reflectionClass
     *
     * @return GeneratorCompose
     */
    private function createCommonTestSource(\ReflectionClass $reflectionClass)
    {
        $sourceGenerator = $this->generatorFactory->getGeneratorCompose();

        $sourceGenerator->addGenerator(
            $this->generatorFactory->getClassGenerator(
                $reflectionClass->getShortName()
            )
        );

        $sourceGenerator->addGenerator(
            $this->generatorFactory->getSetUpMethodGenerator(
                $reflectionClass
            )
        );

        $sourceGenerator = $this->createMethodTestSource($sourceGenerator);

        $sourceGenerator->addGenerator(
            $this->generatorFactory->getTearDownMethodGenerator()
        );

        return $sourceGenerator;
    }

    /**
     * @param GeneratorCompose $sourceGenerator
     *
     * @return GeneratorCompose
     */
    private function createMethodTestSource(
        GeneratorCompose $sourceGenerator
    ) {
        /** @var \ReflectionMethod $method */
        foreach ($this->targetMethodList as $method) {

            $sourceGenerator->addGenerator(
                $this->generatorFactory->getMethodGenerator($method)
            );

            $sourceGenerator->addGenerator(
                $this->generatorFactory->getExceptionMethodGenerator(
                    $method, new AnnotationParser($method->getDocComment())
                )
            );
        }

        return $sourceGenerator;
    }

    /**
     * @param \ReflectionClass $classReflection
     * @param string           $filePath
     * @param string           $source
     *
     * @return int
     */
    private function saveTestFileSource(
        \ReflectionClass $classReflection, $filePath, $source
    ) {
        $testFilePath = $this->createTestFile($classReflection, $filePath);

        if ($testFilePath === true) {
            return false;
        }

        return file_put_contents(
            $testFilePath, $source, FILE_APPEND
        );
    }

    /**
     * @param \ReflectionClass $reflectionClass
     * @param string           $sourceFilePath
     *
     * @return bool|string
     */
    private function createTestFile(
        \ReflectionClass $reflectionClass, $sourceFilePath
    ) {
        $this->testFileCreator->setTargetClassName(
            $reflectionClass->getShortName()
        );

        $this->testFileCreator->setTestFilePath(
            $this->generateTestPath($sourceFilePath)
        );

        return $this->testFileCreator->createFile();
    }
}