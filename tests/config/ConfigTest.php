<?php

namespace Unitgen\Test\config;

use Unitgen\config\Config;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Unitgen\config\exceptions\UnitGenConfigException;

class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    private $config;

    public function setUp()
    {
        $sourcePath = vfsStream::setup('source-path', 777, []);
        $savePath = vfsStream::setup('save-path', 777, []);

        $test
            = "<?php
            return [
                'source'        => [
                    'path'    => [
                        '" . $sourcePath->url() . "' => true,
                    ],
                    'exclude' => [
                        'exclude-path'
                    ]
                ],
                'savePath'      => '" . $savePath->url() . "',
                'classExclude'  => [
                    'name'      => ['ClassName'],
                    'regexp'    => ['#regexp#'],
                    'parent'    => ['ParentClassName'],
                    'implement' => ['Interface'],
                ],
                'methodExclude' => [
                    'name'     => ['methodName'],
                    'regexp'   => ['#regexp#'],
                    'modifier' => ['public', 'protected']
                ]
            ];";

        file_put_contents(__DIR__ . '/test-config.php', $test);

        $this->config = new Config(__DIR__ . '/test-config.php');
    }

    public function testSetConfigPath()
    {
        $this->assertTrue(true);
    }

    public function testSetConfigPathExceptionType()
    {
        $this->expectException(UnitGenConfigException::class);

        $this->config->setConfigPath(['invalidType']);
    }

    public function testSetConfigPathExceptionFileExist()
    {
        $this->expectException(UnitGenConfigException::class);

        $this->config->setConfigPath('invalidPath');
    }

    public function testInvalidConfigExceptionType()
    {
        $this->expectException(UnitGenConfigException::class);

        $test = "<?php return new \\stdClass;";

        file_put_contents(__DIR__ . '/invalid-test-config.php', $test);

        $this->config->setConfigPath(__DIR__ . '/invalid-test-config.php');
    }

    public function testInvalidConfigExceptionRequired()
    {
        $this->expectException(UnitGenConfigException::class);

        $test = "<?php return [];";

        file_put_contents(__DIR__ . '/invalid-test-config.php', $test);

        $this->config->setConfigPath(__DIR__ . '/invalid-test-config.php');
    }

    public function testGetConfigDataEmpty()
    {
        $test
            = "<?php
            return [
                'source' => [],
                'savePath'      => '" . vfsStream::url('save-path') . "',
            ];";

        file_put_contents(__DIR__ . '/test-config.php', $test);

        $this->config = new Config(__DIR__ . '/test-config.php');

        $this->assertEquals([], $this->config->getSourcePath());

        $this->assertEquals([], $this->config->getSourcePathExclude());
    }

    public function testGetSourcePath()
    {
        $this->assertEquals(
            [vfsStream::url('source-path') => true],
            $this->config->getSourcePath()
        );
    }

    public function testGetSourcePathExclude()
    {
        $this->assertEquals(
            ['exclude-path'],
            $this->config->getSourcePathExclude()
        );
    }

    public function testGetSavePath()
    {
        $this->assertEquals(
            vfsStream::url('save-path'), $this->config->getSavePath()
        );
    }

    public function testGetClassExcludeData()
    {
        $classExcludeData = [
            'name'      => ['ClassName'],
            'regexp'    => ['#regexp#'],
            'parent'    => ['ParentClassName'],
            'implement' => ['Interface']
        ];

        $this->assertEquals(
            $classExcludeData, $this->config->getClassExcludeData()
        );
    }

    public function testGetMethodExcludeData()
    {
        $methodExcludeData = [
            'name'     => ['methodName'],
            'regexp'   => ['#regexp#'],
            'modifier' => ['public', 'protected']
        ];

        $this->assertEquals(
            $methodExcludeData, $this->config->getMethodExcludeData()
        );
    }

    public function tearDown()
    {
        unlink(__DIR__ . '/test-config.php');
    }

    public static function tearDownAfterClass()
    {
        unlink(__DIR__ . '/invalid-test-config.php');
    }
}