<?php

declare(strict_types=1);

namespace Kickflip;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kickflip\Events\BaseEvent;
use Kickflip\Events\SiteBuildStarted;
use Kickflip\Events\SiteBuildComplete;
use Kickflip\Models\PageData;
use Illuminate\Console\OutputStyle;

class SiteBuilder
{
    private SourcesLocator $sourcesLocator;

    public function __construct(
        private bool $prettyUrls,
    ) {
        $this->sourcesLocator = new SourcesLocator(KickflipHelper::sourcePath());
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
        foreach ($renderPageList as $page) {
            $consoleOutput->writeln(sprintf('Rendering page from %s', $page->source->getFilename()));
            Logger::verbose("Building " . $page->source->getName() . ":" . $page->url . ":" . $page->title);
            if ($this->prettyUrls) {
                $outputFile = sprintf("%s/index.html", KickflipHelper::buildPath($page->url));
            } else {
                $outputFile = sprintf("%s.html", KickflipHelper::buildPath($page->url));
            }
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
        // TODO: clean up build cache
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

        return $this;
    }
}
