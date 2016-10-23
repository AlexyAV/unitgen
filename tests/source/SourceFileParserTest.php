<?php

use PHPUnit\Framework\TestCase;
use Unitgen\source\exception\SourceException;
use Unitgen\source\SourceFileParser;

class SourceFileParserTest extends TestCase
{
    /**
     * @var SourceFileParser
     */
    private $sourceFileParser;

    /**
     * @var string
     */
    private $defaultTestSourcePath = __DIR__ . '/TestSourcePath.php';

    public function setUp()
    {
        $this->sourceFileParser = new SourceFileParser;
    }

    public function testSetFilePath()
    {
        $sourceData = "<?php

namespace Unitgen\\Test\\source;

/**
 * Class TestSourcePath
 */
class TestSourcePath
{}";

        file_put_contents($this->defaultTestSourcePath, $sourceData);

        $this->assertEquals(
            $this->defaultTestSourcePath,
            $this->sourceFileParser->setFilePath($this->defaultTestSourcePath)
        );

        unlink(__DIR__ . '/TestSourcePath.php');
    }

    public function testSetFilePathExceptionType()
    {
        $this->expectException(SourceException::class);

        $this->sourceFileParser->setFilePath(['invalidSourcePath']);
    }

    public function testSetFilePathExceptionExist()
    {
        $this->expectException(SourceException::class);

        $this->sourceFileParser->setFilePath('invalidSourcePath');
    }

    public function testGetFullClassName()
    {
        $sourceData = "<?php

namespace Unitgen\\Test\\source;

/**
 * Class TestSourcePath
 */
class TestSourcePath
{}";

        file_put_contents($this->defaultTestSourcePath, $sourceData);

        $sourceFileParser = new SourceFileParser($this->defaultTestSourcePath);

        $this->assertEquals(
            $sourceFileParser->getFullClassName(),
            'Unitgen\Test\source\TestSourcePath'
        );

        unlink(__DIR__ . '/TestSourcePath.php');
    }

    public function testGetReflection()
    {
        $sourceData = "<?php

namespace Unitgen\\Test\\source;

/**
 * Class TestSourcePath
 */
class TestSourcePath
{}";

        file_put_contents($this->defaultTestSourcePath, $sourceData);

        $this->sourceFileParser->setFilePath($this->defaultTestSourcePath);

        $this->assertInstanceOf(
            \ReflectionClass::class,
            $this->sourceFileParser->getReflection()
        );

        unlink(__DIR__ . '/TestSourcePath.php');
    }

    public function testGetClassNamespaceFalse()
    {
        $sourceData = "<?php

/**
 * Class TestSourcePath
 */
class TestSourcePath
{}";

        file_put_contents($this->defaultTestSourcePath, $sourceData);

        $this->sourceFileParser->setFilePath($this->defaultTestSourcePath);

        $this->assertNull($this->sourceFileParser->getFullClassName());

        unlink(__DIR__ . '/TestSourcePath.php');
    }

    public function testGetClassNameFromFileNameFalse()
    {
        $sourcePath = __DIR__ . '/1.php';

        $sourceData = "<?php

namespace Unitgen\\Test\\source;

/**
 * Class TestSourcePath
 */
abstract class TestSourcePath
{}";

        file_put_contents($sourcePath, $sourceData);

        $this->sourceFileParser->setFilePath($sourcePath);

        $this->assertNull($this->sourceFileParser->getFullClassName());

        unlink(__DIR__ . '/1.php');
    }
}