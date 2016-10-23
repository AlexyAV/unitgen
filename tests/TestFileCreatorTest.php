<?php

use PHPUnit\Framework\TestCase;
use Unitgen\exceptions\UnitGenException;
use Unitgen\TestFileCreator;

class TestFileCreatorTest extends TestCase
{
    /**
     * @var TestFileCreator
     */
    private $testFileCreator;

    public function setUp()
    {
        $this->testFileCreator = new TestFileCreator;
    }

    public function testSetTargetClassName()
    {
        $this->assertEquals(
            'TestClassName',
            $this->testFileCreator->setTargetClassName('TestClassName')
        );
    }

    public function testSetTargetClassNameExceptionType()
    {
        $this->expectException(UnitGenException::class);

        $this->testFileCreator->setTargetClassName(['TestClassName']);
    }

    public function testSetTargetClassNameExceptionEmpty()
    {
        $this->expectException(UnitGenException::class);

        $this->testFileCreator->setTargetClassName('');
    }

    public function testSetTargetClassNameExceptionFormat()
    {
        $this->expectException(UnitGenException::class);

        $this->testFileCreator->setTargetClassName('12InvalidClassFormat');
    }

    public function testSetTestFilePath()
    {
        $this->assertEquals(
            __DIR__,
            $this->testFileCreator->setTestFilePath(__DIR__)
        );
    }

    public function testSetTestFilePathExceptionType()
    {
        $this->expectException(UnitGenException::class);

        $this->testFileCreator->setTestFilePath([__DIR__]);
    }

    public function testSetTestFilePathExceptionEmpty()
    {
        $this->expectException(UnitGenException::class);

        $this->testFileCreator->setTestFilePath('');
    }

    public function testConstruct()
    {
        $testFileCreator = new TestFileCreator('TestClassName', __DIR__);
    }

    public function testCreateTestFilePath()
    {
        $this->testFileCreator->setTargetClassName('ClassName');

        $this->testFileCreator->setTestFilePath(__DIR__ . '/test-path');

        $this->testFileCreator->createFile();

        $this->assertTrue(
            file_exists(__DIR__ . '/test-path/ClassNameTest.php')
        );

        unlink(__DIR__ . '/test-path/ClassNameTest.php');

        rmdir(__DIR__ . '/test-path');
    }

    public function testCreateTestFilePathExist()
    {
        $this->testFileCreator->setTargetClassName('ClassName');

        mkdir(__DIR__ . '/test-path');

        file_put_contents(__DIR__ . '/test-path/ClassNameTest.php', '');

        $this->testFileCreator->setTestFilePath(__DIR__ . '/test-path');

        $this->testFileCreator->createFile();

        $this->assertTrue(
            file_exists(__DIR__ . '/test-path/ClassNameTest.php')
        );

        unlink(__DIR__ . '/test-path/ClassNameTest.php');

        rmdir(__DIR__ . '/test-path');
    }
}