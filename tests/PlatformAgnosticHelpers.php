<?php

declare(strict_types=1);

namespace KickflipMonoTests;

use function str_replace;

use const DIRECTORY_SEPARATOR;

trait PlatformAgnosticHelpers
{
    public static function agnosticPath(string $path): string
    {
        if (DIRECTORY_SEPARATOR === '/') {
            return $path;
        }

        return str_replace('/', DIRECTORY_SEPARATOR, $path);
    }
}
