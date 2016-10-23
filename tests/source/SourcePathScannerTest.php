<?php

use PHPUnit\Framework\TestCase;
use Unitgen\source\exception\SourceException;
use Unitgen\source\SourcePathScanner;

class SourcePathScannerTest extends TestCase
{
    /**
     * @var SourcePathScanner
     */
    private $sourcePathScanner;

    public function setUp()
    {
        $this->initPath();

        $this->sourcePathScanner = new SourcePathScanner(__DIR__ . '/test/');
    }

    private function initPath()
    {
        mkdir(__DIR__ . '/test/source-path', 0777, true);

        file_put_contents(
            __DIR__ . '/test/first-test-file.php', 'some data'
        );

        file_put_contents(
            __DIR__ . '/test/source-path/second-test-file.php', 'some data'
        );

        file_put_contents(
            __DIR__ . '/test/source-path/not-php-file.txt', 'some data'
        );
    }

    private function destroyPath()
    {
        unlink(__DIR__ . '/test/source-path/not-php-file.txt');
        unlink(__DIR__ . '/test/source-path/second-test-file.php');
        unlink(__DIR__ . '/test/first-test-file.php');
        rmdir(__DIR__ . '/test/source-path');

        rmdir(__DIR__ . '/test');
    }

    public function testFetchTargetFiles()
    {
        $targetFiles = $this->sourcePathScanner->fetchTargetFiles();

        $resultFilesArray = [
            __DIR__ . '/test/source-path/second-test-file.php',
            __DIR__ . '/test/first-test-file.php'
        ];

        $currentFilesArray = [];

        foreach ($targetFiles as $name => $file) {
            $currentFilesArray[] = $name;
        }

        $this->assertEquals($resultFilesArray, $currentFilesArray);
    }

    public function testSetGenerateTestRecursively()
    {
        $this->sourcePathScanner->setGenerateTestRecursively(false);

        $targetFiles = $this->sourcePathScanner->fetchTargetFiles();

        $resultFilesArray = [
            __DIR__ . '/test/first-test-file.php'
        ];

        $currentFilesArray = [];

        foreach ($targetFiles as $name => $file) {
            $currentFilesArray[] = $name;
        }

        $this->assertEquals($resultFilesArray, $currentFilesArray);
    }

    public function tearDown()
    {
        $this->destroyPath();
    }

    public function testSetSourcePathExceptionType()
    {
        $this->expectException(SourceException::class);

        $this->sourcePathScanner->setSourcePath(['path']);
    }

    public function testSetSourcePathExceptionDir()
    {
        $this->expectException(SourceException::class);

        $this->sourcePathScanner->setSourcePath('path');
    }

    public function testSetExcludeDir()
    {
        $this->assertEquals(
            [__DIR__ . '/test/source-path/'],
            $this->sourcePathScanner->setExcludeDir(
                [__DIR__ . '/test/source-path/']
            )
        );

        $targetFiles = $this->sourcePathScanner->fetchTargetFiles();

        $resultFilesArray = [
            __DIR__ . '/test/first-test-file.php'
        ];

        $currentFilesArray = [];

        foreach ($targetFiles as $name => $file) {
            $currentFilesArray[] = $name;
        }

        $this->assertEquals($resultFilesArray, $currentFilesArray);
    }

    public function testSetExcludeDirExceptionDir()
    {
        $this->expectException(SourceException::class);

        $this->sourcePathScanner->setExcludeDir(['invalid-path']);
    }
}