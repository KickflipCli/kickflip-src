<?php

use Illuminate\Support\Str;

/**
 * Get the path relative to the kickflip working dir.
 *
 * @param  string  $path
 * @return string
 */
function kickflip_path($path = ''): string
{
    return app('cwd').($path ? DIRECTORY_SEPARATOR.$path : $path);
}

function leftTrimPath(string $path): string
{
    return ltrim($path, ' \\/');
}

function rightTrimPath(string $path): string
{
    return rtrim($path, ' .\\/');
}

function trimPath(string $path): string
{
    return rightTrimPath(leftTrimPath($path));
}

function relativeUrl(string $url): string
{
    return Str::startsWith($url, 'http') ? $url : '/' . trimPath($url);
}

