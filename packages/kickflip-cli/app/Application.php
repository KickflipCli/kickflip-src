<?php

declare(strict_types=1);

namespace Kickflip;

use LaravelZero\Framework\Application as BaseApplication;

use const DIRECTORY_SEPARATOR;

class Application extends BaseApplication
{
    public static ?string $localBase;

    // phpcs:disable
    /**
     * Get the path to the resources directory.
     *
     * @param string $path
     *
     * @return string
     */
    public function resourcePath($path = '')
    {
        $basePath = static::$localBase ?? $this->basePath;
        return $basePath . DIRECTORY_SEPARATOR . 'resources' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
    // phpcs:enable
}
