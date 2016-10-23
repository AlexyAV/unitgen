<?php

use PHPUnit\Framework\TestCase;
use Unitgen\config\Config;
use Unitgen\Controller;
use Unitgen\filter\factory\FilterFactory;
use Unitgen\source\SourceFileParser;
use Unitgen\source\SourcePathScanner;

/**
 * Генератор базовой структуры для юнит тестов. Генерируемые классы тестов
 * основаны на phpunit версии >=5.*.
 */

class ControllerTest extends TestCase
{
    /**
     * @var Controller
     */
    public $controller;

    public function setUp()
    {
        file_put_contents(
            __DIR__ . '/controllerTestSourcePath/config.php',
            "<?php
return [
    'source'        => [
        'path'    => [
            '" . __DIR__ . '/controllerTestSourcePath' . "' => true,
        ],
    ],
    'savePath'      => '" . __DIR__ . "/tests',
    'classExclude'  => [
        'name'      => [],
        'regexp'    => [],
        'parent'    => [],
        'implement' => [],
    ],
    'methodExclude' => [
        'name'     => [],
        'regexp'   => [],
        'modifier' => []
    ]
];"
        );

        $this->controller = new Controller(
            new Config(__DIR__ . '/controllerTestSourcePath/config.php'),
            new SourcePathScanner,
            new SourceFileParser,
            new FilterFactory
        );
    }

    public function testRunGenerator()
    {
        $this->controller->runGenerator();

        $this->assertEquals(4, $this->controller->getSourceFilesCount());

        $this->assertEquals(1, $this->controller->getTargetClassesCount());

        unlink(__DIR__ . '/controllerTestSourcePath/config.php');

        unlink(
            __DIR__ . '/tests/controllerTestSourcePath/TestSourceClassATest.php'
        );

        unlink(
            __DIR__ . '/tests/controllerTestSourcePath/TestSourceClassBTest.php'
        );
    }
}