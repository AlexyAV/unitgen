<?php

use PHPUnit\Framework\TestCase;
use Unitgen\dispatcher\exceptions\DispatcherException;
use Unitgen\dispatcher\GeneratorDispatcher;
use Unitgen\generator\factory\TestGeneratorFactory;
use Unitgen\generator\GeneratorCompose;
use Unitgen\generator\methodGenerator\fixture\SetUpMethod;
use Unitgen\generator\classGenerator\TestClass;
use Unitgen\generator\methodGenerator\fixture\TearDownMethod;
use Unitgen\generator\methodGenerator\test\ExceptionMethod;
use Unitgen\generator\methodGenerator\test\TestMethod;

class GeneratorDispatcherTest extends TestCase
{
    /**
     * @var GeneratorDispatcher
     */
    private $generatorDispatcher;

    public function setUp()
    {
        $testFileCreatorMock = $this->getMockBuilder(
            \Unitgen\TestFileCreator::class
        )->disableOriginalConstructor()->setMethods(
            ['setTargetClassName', 'setTestFilePath', 'createFile']
        )->getMock();

        $testFileCreatorMock->method('createFile')->willReturn(
            __DIR__ . '/TestTargetClassTest.php'
        );

        $generatorFactoryMock = $this->getMockBuilder(
            TestGeneratorFactory::class
        )->setMethods(
            [
                'getGeneratorCompose',
                'getClassGenerator',
                'getSetUpMethodGenerator',
                'getTearDownMethodGenerator',
                'getExceptionMethodGenerator',
                'getMethodGenerator'
            ]
        )->getMock();

        $generatorFactoryMock->method('getClassGenerator')->willReturn(
            $this->getMockBuilder(TestClass::class)
                ->disableOriginalConstructor()->getMock()
        );

        $generatorFactoryMock->method('getSetUpMethodGenerator')->willReturn(
            $this->getMockBuilder(SetUpMethod::class)
                ->disableOriginalConstructor()->getMock()
        );

        $generatorFactoryMock->method('getTearDownMethodGenerator')->willReturn(
            $this->getMockBuilder(TearDownMethod::class)->getMock()
        );

        $generatorFactoryMock->method('getExceptionMethodGenerator')->willReturn(
            $this->getMockBuilder(ExceptionMethod::class)
                ->disableOriginalConstructor()->getMock()
        );

        $generatorFactoryMock->method('getMethodGenerator')->willReturn(
            $this->getMockBuilder(TestMethod::class)
                ->disableOriginalConstructor()->getMock()
        );

        $generatorComposeMock = $this->getMockBuilder(
            GeneratorCompose::class
        )->setMethods(['addGenerator', 'generate'])->getMock();

        $generatorComposeMock->method('generate')->willReturn(
            'testFileCreated'
        );

        $generatorFactoryMock->method(
            'getGeneratorCompose'
        )->willReturn($generatorComposeMock);

        $this->generatorDispatcher = new GeneratorDispatcher(
            $testFileCreatorMock,
            $generatorFactoryMock,
            new \ReflectionClass(TestTargetClass::class),
            (new \ReflectionClass(TestTargetClass::class))->getMethods(),
            __DIR__
        );
    }

    public function testCreateTest()
    {
        $this->generatorDispatcher->createTest();

        $this->assertTrue(file_exists(__DIR__ . '/TestTargetClassTest.php'));

        $generatedSource = file_get_contents(
            __DIR__ . '/TestTargetClassTest.php'
        );

        $this->assertEquals($generatedSource, 'testFileCreated');

        unlink(__DIR__ . '/TestTargetClassTest.php');
    }

    public function testSetTestPathExceptionType()
    {
        $this->expectException(DispatcherException::class);

        $this->generatorDispatcher->setTestPath(['testPath']);
    }

    public function testSetTestPathExceptionDir()
    {
        $this->expectException(DispatcherException::class);

        $this->generatorDispatcher->setTestPath('testPath');
    }
}

class TestTargetClass {
    public function methodA() {}
    public function methodB() {}
}