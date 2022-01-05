<?php

declare(strict_types=1);

namespace Kickflip;

use Exception;
use Illuminate\Config\Repository;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Kickflip\Enums\CliStateDirPaths;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterParserInterface;

use function app;
use function dirname;
use function getcwd;
use function implode;
use function is_null;
use function ltrim;
use function mix;
use function realpath;
use function rtrim;

use const DIRECTORY_SEPARATOR;

final class KickflipHelper
{
    private static string $basePath;

    /**
     * Get the path relative to the kickflip working dir.
     *
     * @param string|null $key Identifier of the entry to look for.
     * @param T|null $default
     *
     * @return Repository|T|null
     *
     * @template T
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
     * @internal
     */
    public static function basePath(?string $basePath = null): string
    {
        if (
            isset(self::$basePath) === false ||
            $basePath !== null
        ) {
            self::$basePath = realpath($basePath ?? getcwd());
        }

        return self::$basePath;
    }

    public static function setPaths(string $basePath): void
    {
        Application::$localBase = $basePath;
        $kickflipCliState = self::config();
        $baseConfigPath = $basePath . DIRECTORY_SEPARATOR . 'config';
        $kickflipCliState->set('paths', [
            CliStateDirPaths::Base => $basePath,
            CliStateDirPaths::Cache => $basePath . DIRECTORY_SEPARATOR . 'cache',
            CliStateDirPaths::Resources => $basePath . DIRECTORY_SEPARATOR . 'resources',
            CliStateDirPaths::Config => $baseConfigPath,
            CliStateDirPaths::ConfigFile => $baseConfigPath . DIRECTORY_SEPARATOR . 'config.php',
            CliStateDirPaths::EnvConfig => $baseConfigPath . DIRECTORY_SEPARATOR . 'config.{env}.php',
            CliStateDirPaths::BootstrapFile => $baseConfigPath . DIRECTORY_SEPARATOR . 'bootstrap.php',
            CliStateDirPaths::BuildBase => [
                CliStateDirPaths::BuildSourcePart => $basePath . DIRECTORY_SEPARATOR . 'source',
                CliStateDirPaths::EnvBuildDestinationPart => $basePath . DIRECTORY_SEPARATOR . 'build_{env}',
                CliStateDirPaths::BuildDestinationPart => $basePath . DIRECTORY_SEPARATOR . 'build_{env}',
            ],
        ]);

        // Set the base storage path
        app()->useStoragePath($basePath . DIRECTORY_SEPARATOR . 'storage');

        if (app()->hasBeenBootstrapped()) {
            /**
             * @var Repository $config
             */
            $config = app('config');
            $config->set('view.paths', [
                self::resourcePath('views'),
                self::sourcePath(),
            ]);
            $config->set('view.compiled', self::namedPath(CliStateDirPaths::ConfigFile));
        }
    }

    /**
     * Get the named kickflip path.
     */
    public static function namedPath(string $name): string
    {
        /**
         * @var string $path
         */
        $path = self::config('paths.' . $name);

        return $path;
    }

    /**
     * Get the path to a versioned Mix file.
     *
     * @throws Exception
     */
    public static function mix(string $path): HtmlString | string
    {
        return mix($path, implode(DIRECTORY_SEPARATOR, [
            'assets',
            'build',
        ]));
    }

    /**
     * Get the path to a versioned Mix file.
     */
    public static function assetUrl(string $path): HtmlString
    {
        return new HtmlString(
            Str::of($path)->ltrim('/')
                ->prepend('assets/')
                ->prepend(self::config('site.baseUrl')),
        );
    }

    /**
     * Get a file path inside the kickflip working config dir.
     */
    public static function configPath(string $path = ''): string
    {
        return self::namedPath(CliStateDirPaths::Config) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path relative to the kickflip working dir.
     */
    public static function resourcePath(string $path = ''): string
    {
        return self::namedPath(CliStateDirPaths::Resources) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path relative to the kickflip working dir.
     */
    public static function sourcePath(string $path = ''): string
    {
        return self::namedPath(CliStateDirPaths::BuildSource) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    /**
     * Get the path relative to the kickflip working dir.
     */
    public static function buildPath(?string $path = ''): string
    {
        return self::namedPath(CliStateDirPaths::BuildDestination) .
            ($path ? DIRECTORY_SEPARATOR . self::trimPath($path) : $path);
    }

    /**
     * Return the path to the root of the `kickflip-cli` package.
     */
    public static function rootPackagePath(): string
    {
        return dirname(__FILE__, 2);
    }

    public static function getFrontMatterParser(): FrontMatterParserInterface
    {
        return (new FrontMatterExtension())->getFrontMatterParser();
    }

    public static function leftTrimPath(string $path): string
    {
        return ltrim($path, ' .\\/');
    }

    public static function rightTrimPath(string $path): string
    {
        return rtrim($path, ' .\\/');
    }

    public static function trimPath(string $path): string
    {
        return rtrim(ltrim($path, ' .\\/'), ' .\\/');
    }

    public static function relativeUrl(string $url): string
    {
        return Str::startsWith($url, 'http') ? $url : self::trimPath($url);
    }

    public static function toKebab(string $string): string
    {
        return (string) Str::of($string)->kebab();
    }
}
