<?php

namespace Unitgen\config;

/**
 * Interface ConfigInterface
 *
 * @package Unitgen\config
 */
interface ConfigInterface
{
    const SAVE_PATH_PARAM = 'savePath';

    const SOURCE_PATH_PARAM = 'source';

    const CLASS_EXCLUDE_PARAM = 'classExclude';

    const METHOD_EXCLUDE_PARAM = 'methodExclude';

    /**
     * @return array
     */
    public function getSourcePath();

    /**
     * @return array
     */
    public function getSourcePathExclude();

    /**
     * @return string
     */
    public function getSavePath();

    /**
     * @return array
     */
    public function getClassExcludeData();

    /**
     * @return array
     */
    public function getMethodExcludeData();
}