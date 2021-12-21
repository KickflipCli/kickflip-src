<?php

declare(strict_types=1);

namespace Kickflip;

use Illuminate\Config\Repository;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Kickflip\Enums\CliStateDirPaths;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterParserInterface;

final class KickflipHelper
{
    private static string $basePath;

    /**
     * Get the path relative to the kickflip working dir.
     *
     * @template T
     * @param  null|string  $key Identifier of the entry to look for.
     * @param  null|T  $default
     * @return Repository|null|T
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

    /**
     * @param string|null $basePath
     * @return string
     * @internal
     */
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

    public static function setPaths(string $basePath): void
    {
        $kickflipCliState = KickflipHelper::config();
        $kickflipCliState->set('paths', [
            CliStateDirPaths::Base => $basePath,
            CliStateDirPaths::Cache => $basePath . '/cache',
            CliStateDirPaths::Resources => $basePath . '/resources',
            CliStateDirPaths::Config => $basePath . '/config/config.php',
            CliStateDirPaths::EnvConfig => $basePath . '/config/config.{env}.php',
            CliStateDirPaths::BootstrapFile => $basePath . '/config/bootstrap.php',
            CliStateDirPaths::NavigationFile => $basePath . '/config/navigation.php',
            CliStateDirPaths::EnvNavigationFile => $basePath . '/config/navigation.{env}.php',
            CliStateDirPaths::BuildBase => [
                CliStateDirPaths::BuildSourcePart => $basePath . '/source',
                CliStateDirPaths::BuildDestinationPart => $basePath . '/build_{env}',
            ]
        ]);

        if (app()->hasBeenBootstrapped()) {
            /**
             * @var Repository $config
             */
            $config = app('config');
            $config->set('view.paths', [
                KickflipHelper::resourcePath('views'),
                KickflipHelper::sourcePath(),
            ]);
            $config->set('view.compiled', KickflipHelper::namedPath(CliStateDirPaths::Config));
        }
    }

    /**
     * Get the named kickflip path.
     *
     * @param string $name
     * @return string
     */
    public static function namedPath(string $name): string
    {
        return KickflipHelper::config('paths.' . $name);
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
    public static function resourcePath(string $path = ''): string
    {
        return KickflipHelper::namedPath(CliStateDirPaths::Resources).($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path relative to the kickflip working dir.
     *
     * @param  string  $path
     * @return string
     */
    public static function sourcePath(string $path = ''): string
    {
        return KickflipHelper::namedPath(CliStateDirPaths::BuildSource).($path ? DIRECTORY_SEPARATOR.$path : $path);
    }

    /**
     * Get the path relative to the kickflip working dir.
     *
     * @param string|null $path
     * @return string
     */
    public static function buildPath(?string $path = ''): string
    {
        return KickflipHelper::namedPath(CliStateDirPaths::BuildDestination).($path ? DIRECTORY_SEPARATOR.KickflipHelper::trimPath($path) : $path);
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
        return Str::startsWith($url, 'http') ? $url : KickflipHelper::trimPath($url);
    }

    public static function toKebab(string $string): string
    {
        return (string) Str::of($string)->kebab();
    }
}
