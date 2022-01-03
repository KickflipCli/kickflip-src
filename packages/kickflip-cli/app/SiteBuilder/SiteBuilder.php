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
use function collect;
use function view;

final class SiteBuilder
{
    private SourcesLocator $sourcesLocator;
    /**
     * @var ShikiNpmFetcher
     */
    private ShikiNpmFetcher $shikiNpmFetcher;

    public function __construct(
    ) {
        $this->sourcesLocator = app(SourcesLocator::class);

        $this->shikiNpmFetcher = app(ShikiNpmFetcher::class);
        if (!$this->shikiNpmFetcher->isShikiDownloaded()) {
            $this->shikiNpmFetcher->installShiki();
        }
    }

    public static function includeEnvironmentConfig(string $env)
    {
        /**
         * @var Repository $kickflipCliState
         */
        $kickflipCliState = KickflipHelper::config();
        $envConfigPath = (string) Str::of(KickflipHelper::namedPath(CliStateDirPaths::EnvConfig))->replaceEnv($env);
        if (file_exists($envConfigPath)) {
            $envSiteConfig = include $envConfigPath;
            $kickflipCliState->set('site', array_merge($kickflipCliState->get('site'), $envSiteConfig));
        }

        // Share site config into global View data...
        View::share(
            'site',
            SiteData::fromConfig($kickflipCliState->get('site'))
        );
    }

    public static function updateBuildPaths(string $env)
    {
        /**
         * @var Repository $kickflipCliState
         */
        $kickflipCliState = KickflipHelper::config();
        $buildDestinationBasePath = KickflipHelper::namedPath(CliStateDirPaths::EnvBuildDestination);
        $buildDestinationEnvPath = (string) Str::of($buildDestinationBasePath)->replaceEnv($env);
        // TODO: decide if we need a views entry in here too...
        $kickflipCliState->set('paths.' . CliStateDirPaths::BuildDestination, $buildDestinationEnvPath);
    }

    public function build($consoleOutput): void
    {
        $this->fireEvent(SiteBuildStarted::class)
            ->copyAssets($consoleOutput)
            ->buildSite($consoleOutput)
            ->fireEvent(SiteBuildComplete::class)
            ->cleanup();
    }

    private function fireEvent(string $eventClass): self
    {
        /**
         * @var BaseEvent $eventClass
         */
        $eventClass::dispatch();
        return $this;
    }

    private function buildSite(OutputStyle $consoleOutput): self
    {
        $renderPageList = $this->sourcesLocator->getRenderPageList();
        $consoleOutput->writeln(sprintf('<info>Found %d pages to render into HTML...</info>', count($renderPageList)));
        Logger::veryVerboseTable(
            ["Page Title", "Page URL", "Page Source", "Source Type"],
            collect($renderPageList)->sortBy('url')->map(function (PageData $page) {
                return [$page->title, $page->url, $page->source->getFilename(), $page->source->getType()];
            })->toArray()
        );
        /**
         * @var PageData $page
         */
        foreach ($renderPageList as $page) {
            $consoleOutput->writeln(sprintf('Rendering page from %s', $page->source->getFilename()));
            Logger::verbose("Building " . $page->source->getName() . ":" . $page->url . ":" . $page->title);
            $outputFile = $page->getOutputPath();
            $outputDir = dirname($outputFile);
            $view = view($page->source->getName(), [
                'page' => $page
            ]);
            if (!is_dir($outputDir)) {
                mkdir(directory: $outputDir, recursive: true);
            }
            file_put_contents($outputFile, $view->render());
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
            $consoleOutput->writeln("Assets folder found, copying to build dir.");
            Logger::verbose("Copying assets from {$kickflipSourceDir} to {$kickflipBuildDir}");
            File::copyDirectory($kickflipSourceDir, $kickflipBuildDir);
            $consoleOutput->writeln("Assets folder copied to build dir.");
        } else {
            $consoleOutput->warning("Assets folder NOT found, these may be missing.");
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
