<?php

declare(strict_types=1);

namespace Kickflip;

use Illuminate\Config\Repository;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterParserInterface;

final class KickflipHelper
{
    private static string $basePath;

    public static function basePath(?string $basePath = null): string
    {
        if (
            isset(KickflipHelper::$basePath) === false ||
            $basePath !== null
        ) {
            KickflipHelper::$basePath = realpath($basePath ?? getcwd());
        }
        return KickflipHelper::$basePath;
    }

    /**
     * Get the path relative to the kickflip working dir.
     *
     * @param  ?string  $key Identifier of the entry to look for.
     * @param  ?mixed  $default
     * @return Repository|string|array|int|float|null
     */
    public static function config(?string $key = null, mixed $default = null)
    {
        /**
         * @var Repository $kickflipState
         */
        $kickflipState = app('kickflipCli');
        if (is_null($key)) {
            return $kickflipState;
        }

        return $kickflipState->get($key, $default);
    }

    public static function setPaths(string $basePath): void
    {
        $kickflipCliState = KickflipHelper::config();
        $kickflipCliState->set('paths', [
            'baseDir' => $basePath,
            'cache' => $basePath . '/cache',
            'resources' => $basePath . '/resources',
            'config' => $basePath . '/config/config.php',
            'env_config' => $basePath . '/config/config.{env}.php',
            'bootstrapFile' => $basePath . '/config/bootstrap.php',
            'navigationFile' => $basePath . '/config/navigation.php',
            'env_navigationFile' => $basePath . '/config/navigation.{env}.php',
            'build' => [
                'source' => $basePath . '/source',
                'destination' => $basePath . '/build_{env}',
            ]
        ]);

        if (app()->hasBeenBootstrapped()) {
            /**
             * @var Repository $config
             */
            $config = app('config');
            $config->set('view.paths', [
                KickflipHelper::resourcePath('views'),
                KickflipHelper::path('source'),
            ]);
            $config->set('view.compiled', KickflipHelper::config('paths.cache'));
        }
    }

    /**
     * Get the path to a versioned Mix file.
     *
     * @param string $path
     * @return HtmlString|string
     *
     * @throws \Exception
     */
    public static function mix(string $path): HtmlString|string
    {
        static $baseUrl;
        if (is_null($baseUrl)) {
            $baseUrl = KickflipHelper::config('site.baseUrl', '');
        }

        return new HtmlString(
            $baseUrl . mix($path, 'assets/build')
        );
    }

    /**
     * Get the path to a versioned Mix file.
     *
     * @param  string  $path
     * @return HtmlString
     */
    public static function assetUrl(string $path): HtmlString
    {
        return new HtmlString(
            Str::of($path)->ltrim('/')
                ->prepend('assets/')
                ->prepend(KickflipHelper::config('site.baseUrl'))
        );
    }

    /**
     * Get the path relative to the kickflip working dir.
     *
     * @param string $path
     * @return string
     */
    public static function path(string $path = ''): string
    {
        return KickflipHelper::$basePath.($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path relative to the kickflip working dir.
     *
     * @param string $path
     * @return string
     */
    public static function resourcePath(string $path = ''): string
    {
        return KickflipHelper::config('paths.resources').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path relative to the kickflip working dir.
     *
     * @param  string  $path
     * @return string
     */
    public static function sourcePath(string $path = ''): string
    {
        return KickflipHelper::config('paths.build.source').($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path relative to the kickflip working dir.
     *
     * @param string|null $path
     * @return string
     */
    public static function buildPath(?string $path = ''): string
    {
        return KickflipHelper::config('paths.build.destination') . ($path ? DIRECTORY_SEPARATOR.KickflipHelper::trimPath($path) : $path);
    }

    public static function getFrontMatterParser(): FrontMatterParserInterface
    {
        return (new FrontMatterExtension())->getFrontMatterParser();
    }

    public static function leftTrimPath(string $path): string
    {
        return ltrim($path, ' \\/');
    }

    public static function rightTrimPath(string $path): string
    {
        return rtrim($path, ' .\\/');
    }

    public static function trimPath(string $path): string
    {
        return rtrim(ltrim($path, ' \\/'), ' .\\/');
    }

    public static function relativeUrl(string $url): string
    {
        return Str::startsWith($url, 'http') ? $url : '/' . KickflipHelper::trimPath($url);
    }

    public static function toKebab(string $string): string
    {
        return (string) Str::of($string)->kebab();
    }
}
