<?php

namespace Unitgen\config;

use Unitgen\config\exceptions\UnitGenConfigException;

/**
 * Class Config
 *
 * @package Unitgen\config
 */
class Config implements ConfigInterface
{
    /**
     * @var array List of required config params
     */
    protected $requiredParams
        = [
            self::SAVE_PATH_PARAM,
            self::SOURCE_PATH_PARAM
        ];

    /**
     * @var string Path to client config
     */
    protected $configPath;

    /**
     * @var array|null Current config data
     */
    protected $config;

    /**
     * Config constructor.
     *
     * @param string|null $configPath
     */
    public function __construct($configPath)
    {
        $this->setConfigPath($configPath);
    }

    /**
     * Set client config path.
     *
     * @param string $configPath
     *
     * @return string Current config path
     * @throws \Exception
     */
    public function setConfigPath($configPath)
    {
        if (!is_string($configPath)) {
            throw new UnitGenConfigException(
                'Unitgen config path must be a string.'
                . gettype($configPath) . ' given.'
            );
        }

        $preparedConfigPath = realpath(trim($configPath));

        if (!file_exists($configPath) && !is_file($configPath)) {
            throw new UnitGenConfigException(
                'Specified unit-gen config file does not exist.'
            );
        }

        $this->configPath = $preparedConfigPath;

        $this->loadConfig();

        return $this->configPath;
    }

    /**
     * Validate config syntax.
     *
     * @param string $config
     *
     * @return bool
     */
    protected function validateConfig($config)
    {
        $isValid = true;

        if (!is_array($config)) {
            $isValid = false;
        }

        // Check for required params
        foreach ($this->requiredParams as $param) {
            if (!array_key_exists($param, $config)) {
                $isValid = false;

                break;
            }
        }

        return $isValid;
    }

    /**
     * Load client config data.
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function loadConfig()
    {
        $config = require $this->configPath;

        $isValid = $this->validateConfig($config);

        if (!$isValid) {
            throw new UnitGenConfigException(
                'Unitgen config file has incorrect format.'
            );
        }

        $this->config = $config;

        return $this->config;
    }

    /**
     * @param string $type
     *
     * @return array
     */
    protected function getConfigData($type)
    {
        return $this->config[$type];
    }

    /**
     * Get target files from client config.
     *
     * @return array
     */
    public function getSourcePath()
    {
        $sourcePathData = $this->getConfigData(self::SOURCE_PATH_PARAM);

        if (!array_key_exists('path', $sourcePathData)) {
            return [];
        }

        return $sourcePathData['path'];
    }

    /**
     * Get files exclude list from client config.
     *
     * @return array
     */
    public function getSourcePathExclude()
    {
        $sourcePathData = $this->getConfigData(self::SOURCE_PATH_PARAM);

        if (!array_key_exists('exclude', $sourcePathData)) {
            return [];
        }

        return $sourcePathData['exclude'];
    }

    /**
     * Get path to save tests.
     *
     * @return string
     */
    public function getSavePath()
    {
        return $this->getConfigData(self::SAVE_PATH_PARAM) ?: '';
    }

    /**
     * Get class exclude list from client config.
     *
     * @return array
     */
    public function getClassExcludeData()
    {
        return $this->getConfigData(self::CLASS_EXCLUDE_PARAM);
    }

    /**
     * Get method exclude list from client config.
     *
     * @return array
     */
    public function getMethodExcludeData()
    {
        return $this->getConfigData(self::METHOD_EXCLUDE_PARAM);
    }
}