<?php

namespace KickflipMonoTests;

trait PlatformAgnosticHelpers
{
    public static function agnosticPath(string $path): string
    {
        if ('/' === DIRECTORY_SEPARATOR) {
            return $path;
        }
        return str_replace('/', DIRECTORY_SEPARATOR, $path);
    }
}
