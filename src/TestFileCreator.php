<?php

namespace Unitgen;

use Unitgen\exceptions\UnitGenException;

/**
 * Class TestFileCreator
 *
 * @package Unitgen
 */
class TestFileCreator
{
    const TEST_FILE_NAME_END = 'Test.php';

    /**
     * @var string
     */
    private $targetClassName;

    /**
     * @var string
     */
    private $testFilePath;

    /**
     * TestFileCreator constructor.
     *
     * @param string $targetClassName
     * @param string $testFilePath
     */
    public function __construct($targetClassName = null, $testFilePath = null)
    {
        if ($targetClassName) {
            $this->setTargetClassName($targetClassName);
        }

        if ($testFilePath) {
            $this->setTestFilePath($testFilePath);
        }
    }

    /**
     * Create test file.
     *
     * @return bool|string
     * @throws UnitGenException
     */
    public function createFile()
    {
        $this->createTestFilePath();

        if ($this->checkTestFileExist()) {
            return true;
        }

        file_put_contents($this->getTestClassName(), "<?php\n\n");

        return $this->getTestClassName();
    }

    /**
     * Recursively create dir structure for new test file.
     *
     * @return bool
     * @throws UnitGenException
     */
    private function createTestFilePath()
    {
        if (is_dir($this->testFilePath)) {
            return true;
        }

        mkdir($this->testFilePath, 0777, true);

        return true;
    }

    /**
     * Check for appropriate target class name.
     *
     * @param string $targetClassName
     *
     * @return null|string
     */
    private function validateTargetClassName($targetClassName)
    {
        $error = null;

        if (!is_string($targetClassName)) {
            $error = 'Target class name must be a string. '
                . gettype($targetClassName) . ' given';

            return $error;
        }

        $preparedTargetClassName = trim($targetClassName);

        if (empty($preparedTargetClassName)) {
            $error = 'Target class name can not be empty.';

            return $error;
        }

        // Check for valid class name format
        if (
        !preg_match(
            '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff\x2f]*$/',
            $preparedTargetClassName
        )
        ) {
            $error = 'Target class has incorrect format.';

            return $error;
        }

        return $error;
    }

    /**
     * @param $testFilePath
     *
     * @return null|string
     */
    private function validateTestFilePath($testFilePath)
    {
        $error = null;

        if (!is_string($testFilePath)) {
            $error = 'Test file path must be a string. '
                . gettype($testFilePath) . ' given';

            return $error;
        }

        $preparedTestFilePath = trim($testFilePath);

        if (empty($preparedTestFilePath)) {
            $error = 'Test file path can not be empty.';

            return $error;
        }

        return $error;
    }

    /**
     * Set the class name to which the file will be generated.
     *
     * @param string $targetClassName
     *
     * @return string
     * @throws UnitGenException
     */
    public function setTargetClassName($targetClassName)
    {
        $error = $this->validateTargetClassName($targetClassName);

        if ($error) {
            throw new UnitGenException($error);
        }

        $this->targetClassName = trim($targetClassName);

        return $this->targetClassName;
    }

    /**
     * Set path for save generated test file.
     *
     * @param string $testFilePath
     *
     * @return string
     * @throws UnitGenException
     */
    public function setTestFilePath($testFilePath)
    {
        $error = $this->validateTestFilePath($testFilePath);

        if ($error) {
            throw new UnitGenException($error);
        }

        $this->testFilePath = trim($testFilePath);

        return $this->testFilePath;
    }

    /**
     * @return bool
     */
    private function checkTestFileExist()
    {
        return file_exists($this->getTestClassName());
    }

    /**
     * @return string
     */
    private function getTestClassName()
    {
        return $this->testFilePath . DIRECTORY_SEPARATOR .
        $this->targetClassName .
        self::TEST_FILE_NAME_END;
    }
}