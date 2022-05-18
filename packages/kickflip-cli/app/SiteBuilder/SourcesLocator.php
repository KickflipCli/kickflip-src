<?php

declare(strict_types=1);

namespace Kickflip\SiteBuilder;

use BadMethodCallException;
use Illuminate\Support\Str;
use Kickflip\Collection\CollectionConfig;
use Kickflip\Collection\PageCollection;
use Kickflip\KickflipHelper;
use Kickflip\Models\ContentFileData;
use Kickflip\Models\PageData;
use Kickflip\Models\SourcePageMetaData;
use Symfony\Component\Finder\SplFileInfo;

use function array_flip;
use function array_map;
use function collect;
use function file_get_contents;
use function strcmp;
use function uasort;

final class SourcesLocator
{
    /**
     * @var array<int, PageData>
     */
    private array $renderPageList;

    /**
     * @var SourcePageMetaData[]
     */
    private array $bladeSources = [];

    /**
     * @var SourcePageMetaData[]
     */
    private array $markdownSources = [];

    /**
     * @var SourcePageMetaData[]
     */
    private array $markdownBladeSources = [];

    /**
     * @var ContentFileData[]
     */
    private array $plainTextOrMediaSources = [];

    public function __construct(
        private string $sourcesBasePath,
    ) {
        $this->discoverSourceFiles();
        // TODO: add a step that discovers and adds items based on collections too...
        $this->discoverCollections();
        $this->buildRenderList();
    }

    private function discoverSourceFiles(): void
    {
        // Filter out anything in the `assets` folder or `_` prefixed folders for collections.
        // Files from `assets` were compiled from mix into that folder before compiling the site.
        $allSourceFiles = KickflipHelper::getFiles($this->sourcesBasePath)
            ->reject(static fn (SplFileInfo $value) => Str::of($value->getRelativePath())->startsWith('_'))
            ->reject(static fn (SplFileInfo $value) => Str::of($value->getRelativePath())->startsWith('assets'));
        $sourcesCount = $allSourceFiles->count();
        for ($i = 0; $i < $sourcesCount; $i++) {
            /**
             * @var SplFileInfo $fileInfo
             */
            $fileInfo = $allSourceFiles->shift();
            $pageMetaData = SourcePageMetaData::fromSplFileInfo($fileInfo);
            match ($pageMetaData->getExtension()) {
                'blade.php' => $this->bladeSources[] = $pageMetaData,
                'md', 'markdown' => $this->markdownSources[] = $pageMetaData,
                'md.blade.php', 'blade.md', 'blade.markdown' => $this->markdownBladeSources[] = $pageMetaData,
                'html', 'txt', 'ico' => $this->plainTextOrMediaSources[] = ContentFileData::make($pageMetaData),
                default => 'do nothing',
            };
        }
    }

    private function discoverCollections(): void
    {
        if (! KickflipHelper::hasItemCollections()) {
            return;
        }

        $kickflipCliState = KickflipHelper::getKickflipState();
        /**
         * @var CollectionConfig[] $collections
         */
        $collections = $kickflipCliState->get('site.collections');
        $itemCollections = [];
        foreach ($collections as $collectionConfig) {
            $itemCollections[$collectionConfig->name] = PageCollection::fromConfig($collectionConfig)->discoverItems();
        }
        $kickflipCliState->set('collections', $itemCollections);
    }

    private function buildRenderList(): void
    {
        $renderList = collect($this->bladeSources)
            ->merge($this->markdownSources)
            ->merge($this->markdownBladeSources)
            ->sort(static fn ($fileOne, $fileTwo) => strcmp($fileOne->getName(), $fileTwo->getName()))
            ->sort(static fn ($fileOne, $fileTwo) => strcmp(
                $fileOne->getRelativeDirectoryPath(),
                $fileTwo->getRelativeDirectoryPath(),
            ));

        // Compile source pages into PageData objects
        $renderList->map(function (SourcePageMetaData $sourcePageMetaData) {
            $frontMatterData = KickflipHelper::getFrontMatterParser()
                    ->parse(file_get_contents($sourcePageMetaData->getFullPath()))
                    ->getFrontMatter() ?? [];
            $this->renderPageList[] = PageData::make(
                $sourcePageMetaData,
                $frontMatterData,
            );
        });

        // Skip doing this if there are no item collections in the config...
        if (! KickflipHelper::hasItemCollections()) {
            return;
        }
        /**
         * @var PageCollection[] $itemCollections
         */
        $itemCollections = KickflipHelper::config('collections');
        // Sort item collections based on collection name...
        uasort(
            $itemCollections,
            static fn (PageCollection $collectionOne, PageCollection $collectionTwo) => strcmp(
                $collectionOne->name,
                $collectionTwo->name,
            ),
        );

        foreach ($itemCollections as $itemCollection) {
            $collectionItems = [];
            $sourcesCount = $itemCollection->sourceItems->count();
            for ($i = 0; $i < $sourcesCount; $i++) {
                /**
                 * @var SourcePageMetaData $sourcePageMetaData
                 */
                $sourcePageMetaData = $itemCollection->sourceItems->get($i);
                $frontMatterData = KickflipHelper::getFrontMatterParser()
                        ->parse(file_get_contents($sourcePageMetaData->getFullPath()))
                        ->getFrontMatter() ?? [];
                $pageData = PageData::makeFromCollection(
                    itemCollection: $itemCollection,
                    collectionIndex: $i,
                    metaData: $sourcePageMetaData,
                    frontMatter: $frontMatterData,
                );
                $collectionItems[] = $pageData;
            }
            $itemCollection->loadItems($collectionItems, $this->renderPageList);
        }
    }

    /**
     * @return array<int, PageData>
     */
    public function getRenderPageList(): array
    {
        return $this->renderPageList;
    }

    public function getRenderPageByName(string $name): PageData
    {
        $nameKeys = array_flip(array_map(
            fn (PageData $page) => $page->source->getName(),
            $this->renderPageList,
        ));

        if (!isset($nameKeys[$name])) {
            throw new BadMethodCallException("Cannot find source file by name: '$name'");
        }

        return $this->renderPageList[$nameKeys[$name]];
    }

    /**
     * @return ContentFileData[]
     */
    public function getCopyFileList(): array
    {
        return $this->plainTextOrMediaSources;
    }
}
