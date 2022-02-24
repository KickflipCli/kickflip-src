<?php

declare(strict_types=1);

namespace Kickflip\SiteBuilder;

use Illuminate\Config\Repository;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Kickflip\Enums\CliStateDirPaths;
use Kickflip\Events\BaseEvent;
use Kickflip\Events\SiteBuildComplete;
use Kickflip\Events\SiteBuildStarted;
use Kickflip\KickflipHelper;
use Kickflip\Logger;
use Kickflip\Models\PageData;
use Kickflip\Models\SiteData;

use function app;
use function array_merge;
use function collect;
use function config;
use function count;
use function dirname;
use function file_exists;
use function file_put_contents;
use function in_array;
use function is_dir;
use function mkdir;
use function rtrim;
use function sprintf;
use function view;

final class SiteBuilder
{
    private SourcesLocator $sourcesLocator;
    private ShikiNpmFetcher $shikiNpmFetcher;

    public function __construct()
    {
        $this->sourcesLocator = app(SourcesLocator::class);

        $this->shikiNpmFetcher = app(ShikiNpmFetcher::class);
        if (!$this->shikiNpmFetcher->isShikiDownloaded()) {
            $this->shikiNpmFetcher->installShiki();
        }
    }

    public static function includeEnvironmentConfig(string $env): void
    {
        /**
         * @var Repository $kickflipCliState
         */
        $kickflipCliState = KickflipHelper::config();
        $envConfigPath = (string) Str::of(KickflipHelper::namedPath(CliStateDirPaths::EnvConfig))->replaceEnv($env);
        if (file_exists($envConfigPath)) {
            $envSiteConfig = include $envConfigPath;
            if (
                in_array($env, [
                    'prod',
                    'production',
                ]) && !isset($envSiteConfig['minifyHtml'])
            ) {
                $kickflipCliState->set('minify_html', true);
            }
            $kickflipCliState->set('site', array_merge($kickflipCliState->get('site'), $envSiteConfig));
            self::updateAppUrl();
        }

        // Share site config into global View data...
        View::share(
            'site',
            SiteData::fromConfig($kickflipCliState->get('site')),
        );

        // Set language...
        config()->set('app.locale', $kickflipCliState->get('site.locale', 'en'));

        self::initCollections();
    }

    public static function updateAppUrl(): void
    {
        /**
         * @var Repository $kickflipCliState
         */
        $kickflipCliState = KickflipHelper::config();
        $baseUrl = $kickflipCliState->get('site.baseUrl');
        if ($baseUrl !== '') {
            $baseUrl = (string) Str::of($kickflipCliState->get('site.baseUrl'))->rtrim('/')->append('/');
            $kickflipCliState->set('site.baseUrl', $baseUrl);
        }
        app('config')->set('app.url', $baseUrl);
        if ($kickflipCliState->has('site.mixUrl')) {
            $mixUrl = rtrim($kickflipCliState->get('site.mixUrl'), '/');
        } else {
            $mixUrl = rtrim($baseUrl, '/');
        }
        app('config')->set('app.mix_url', $mixUrl);
    }

    public static function updateBuildPaths(string $env): void
    {
        /**
         * @var Repository $kickflipCliState
         */
        $kickflipCliState = KickflipHelper::config();
        $buildDestinationBasePath = KickflipHelper::namedPath(CliStateDirPaths::EnvBuildDestination);
        $buildDestinationEnvPath = (string) Str::of($buildDestinationBasePath)->replaceEnv($env);
        $kickflipCliState->set('paths.' . CliStateDirPaths::BuildDestination, $buildDestinationEnvPath);
    }

    public static function initCollections()
    {
        $kickflipCliState = KickflipHelper::config();
        $collections = $kickflipCliState->get('site.collections');
        if ($collections !== null) {
            // TODO: Figure out appropriate validation steps, or similar for init...
            Logger::debug('Validated Collections');
        }
    }

    public function build(OutputStyle $consoleOutput): void
    {
        $this->fireEvent(SiteBuildStarted::class)
            ->copyAssets($consoleOutput)
            ->buildSite($consoleOutput)
            ->fireEvent(SiteBuildComplete::class)
            ->cleanup();
    }

    /**
     * @param class-string $eventClass
     *
     * @return $this
     */
    private function fireEvent(string $eventClass): self
    {
        /**
         * @var BaseEvent $eventClassName
         */
        $eventClassName = $eventClass;
        $eventClassName::dispatch();

        return $this;
    }

    private function buildSite(OutputStyle $consoleOutput): self
    {
        $renderPageList = $this->sourcesLocator->getRenderPageList();
        $consoleOutput->writeln(sprintf('<info>Found %d pages to render into HTML...</info>', count($renderPageList)));
        Logger::veryVerboseTable(
            ['Page Title', 'Page URL', 'Page Source', 'Source Type'],
            collect($renderPageList)
                ->sortBy('url')
                // phpcs:disable
                ->map(function (PageData $page) {
                    return [
                        $page->title,
                        $page->url,
                        $page->source->getFilename(),
                        $page->source->getType(),
                    ];
                })
                // phpcs:enable
                ->toArray(),
        );

        /**
         * @var PageData $page
         */
        foreach ($renderPageList as $page) {
            KickflipHelper::config()->set('page', $page);
            $consoleOutput->writeln(sprintf(
                'Rendering page `%s` from `%s` ',
                $page->getUrl(),
                $page->source->getRelativePath(),
            ));
            Logger::verbose('Building ' . $page->source->getName() . ':' . $page->url . ':' . $page->title);
            $outputFile = $page->getOutputPath();
            $outputDir = dirname($outputFile);
            $view = view($page->source->getName(), [
                'page' => $page,
            ]);
            if (!is_dir($outputDir)) {
                mkdir(directory: $outputDir, recursive: true);
            }
            // Pre-render view and beautify output...
            file_put_contents($outputFile, HtmlFormatter::render($view));
            KickflipHelper::config()->set('page', null);
        }
        $consoleOutput->writeln('<info>Completed page rendering.</info>');

        return $this;
    }

    private function cleanup(): void
    {
        if (!$this->shikiNpmFetcher->isNpmUsedByProject()) {
            $this->shikiNpmFetcher->removeShikiAndNodeModules();
        }
    }

    private function copyAssets(OutputStyle $consoleOutput): self
    {
        $kickflipSourceDir = KickflipHelper::sourcePath('assets');
        $kickflipBuildDir = KickflipHelper::buildPath('assets');

        if (File::isDirectory($kickflipSourceDir)) {
            $consoleOutput->writeln('Assets folder found, copying to build dir.');
            Logger::verbose("Copying assets from {$kickflipSourceDir} to {$kickflipBuildDir}");
            File::copyDirectory($kickflipSourceDir, $kickflipBuildDir);
            $consoleOutput->writeln('Assets folder copied to build dir.');
        } else {
            $consoleOutput->warning('Assets folder NOT found, these may be missing.');
        }

        $rootBuildDir = KickflipHelper::buildPath();
        foreach ($this->sourcesLocator->getCopyFileList() as $copyFileItem) {
            Logger::verbose("Copying asset `{$copyFileItem->getUrl()}` from {$kickflipSourceDir} to {$rootBuildDir}");
            File::ensureDirectoryExists(dirname($copyFileItem->getOutputPath()));
            File::copy(KickflipHelper::sourcePath($copyFileItem->getUrl()), $copyFileItem->getOutputPath());
        }

        return $this;
    }
}
