<?php

declare(strict_types=1);

namespace Kickflip;

use Exception;
use Illuminate\Config\Repository;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Pure;
use Kickflip\Enums\CliStateDirPaths;
use Kickflip\Models\PageData;
use Kickflip\SiteBuilder\UrlHelper;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterParserInterface;
use RuntimeException;
use Symfony\Component\Finder\Finder;

use function app;
use function collect;
use function dirname;
use function getcwd;
use function is_null;
use function iterator_to_array;
use function ltrim;
use function mix;
use function parse_url;
use function realpath;
use function rtrim;

use const DIRECTORY_SEPARATOR;
use const PHP_URL_PATH;

final class KickflipHelper
{
    private static string $basePath;
    private static Repository | null $kickflipState = null;

    public static function bootKickflipState(Repository $state)
    {
        self::$kickflipState = $state;
    }

    public static function getKickflipState(): Repository
    {
        if (self::$kickflipState === null) {
            throw new RuntimeException('Cannot access Kickflip state before initialized.');
        }

        return self::$kickflipState;
    }

    /**
     * Get the path relative to the kickflip working dir.
     *
     * @param string|null $key Identifier of the entry to look for.
     * @param T|null $default
     *
     * @return Repository|T|mixed|null
     *
     * @template T
     */
    public static function config(?string $key = null, mixed $default = null)
    {
        $kickflipState = self::getKickflipState();
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
        return mix($path, 'assets/build');
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

    #[Pure]
    public static function pageRouteName(PageData $pageData): string
    {
        return $pageData->source->getRouteName();
    }

    public static function pageUrl(PageData $pageData): string
    {
        return UrlHelper::getPageUrl($pageData);
    }

    public static function relativeUrl(string $url): string
    {
        $baseUrl = self::config('site.baseUrl', '');
        if (Str::startsWith($url, 'http')) {
            return self::leftTrimPath(Str::replaceFirst($baseUrl, '', $url));
        }

        $baseStringStrip = parse_url(
            $baseUrl,
            PHP_URL_PATH,
        ) ?: '/';
        if ($baseStringStrip === '/') {
            return self::leftTrimPath($url);
        }

        return self::leftTrimPath(Str::replaceFirst($baseStringStrip, '', $url));
    }

    public static function toKebab(string $string): string
    {
        return (string) Str::of($string)->kebab();
    }

    public static function urlFromSource(string $name): string
    {
        return UrlHelper::getSourceFileUrl($name);
    }

    public static function hasItemCollections(): bool
    {
        return self::config()->has('site.collections');
    }

    public static function getFiles(string $path): Collection
    {
        return collect(iterator_to_array(
            Finder::create()
                ->files()
                ->ignoreDotFiles(true)
                ->in($path)
                ->sortByName(),
            false,
        ));
    }
}
