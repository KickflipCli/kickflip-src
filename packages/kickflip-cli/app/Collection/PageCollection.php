<?php

declare(strict_types=1);

namespace Kickflip\Collection;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kickflip\Models\ContentFileData;
use Kickflip\Models\PageData;
use Kickflip\Models\SourcePageMetaData;
use Kickflip\View\KickflipPaginator;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function collect;
use function dirname;
use function is_dir;
use function iterator_to_array;

class PageCollection
{
    public string $name;

    public CollectionConfig $config;

    /**
     * @var Collection of SourcePageMetaData
     */
    public Collection $sourceItems;

    /**
     * @var Collection of PageData
     */
    public Collection $pageItems;

    private function __construct(CollectionConfig $config)
    {
        $this->name = $config->name;
        $this->config = $config;
        $this->sourceItems = collect();
    }

    public static function fromConfig(CollectionConfig $config): PageCollection
    {
        return new PageCollection($config);
    }

    public function discoverItems(): static
    {
        $config = $this->config;
        if (is_dir($config->basePath)) {
            $collectionSourceFiles = collect(iterator_to_array(
                Finder::create()
                    ->files()
                    ->ignoreDotFiles(true)
                    ->in(dirname($config->basePath))
                    ->sortByName(),
                false,
            ))
                ->reject(
                    static fn (SplFileInfo $value) => ! Str::of($value->getRelativePath())->startsWith($config->path),
                )
                ->values();

            $sourcesCount = $collectionSourceFiles->count();
            for ($i = 0; $i < $sourcesCount; $i++) {
                /**
                 * @var SplFileInfo $fileInfo
                 */
                $fileInfo = $collectionSourceFiles->shift();
                $pageMetaData = SourcePageMetaData::fromSplFileInfoForCollection($config, $fileInfo);
                match ($pageMetaData->getExtension()) {
                    'blade.php', 'md', 'markdown',
                    'md.blade.php', 'blade.md', 'blade.markdown' => $this->sourceItems[] = $pageMetaData,
                    'html', 'txt', 'ico' => $this->sourceItems[] = ContentFileData::make($pageMetaData),
                    default => 'do nothing',
                };
            }
        }

        return $this;
    }

    /**
     * @param array<PageData> $collectionItems
     * @param array<int, PageData> &$renderPageList
     */
    public function loadItems(array $collectionItems, array &$renderPageList)
    {
        $this->pageItems = (new SortHandler($this->config))($collectionItems);
        foreach ($this->pageItems as $key => $collectionItem) {
            $collectionItem->updateCollectionIndex($key);
            $renderPageList[] = $collectionItem;
        }
    }

    public function getItems(): Collection
    {
        return $this->pageItems;
    }

    public function backAndNextPaginate(PageData $page): Paginator
    {
        $slice = $this->pageItems->slice($page->getCollectionIndex() - 1, 2)->values();

        return new KickflipPaginator(
            $slice,
            1,
            $page->getCollectionIndex(),
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ],
        );
    }

    public function paginate(PageData $page, ?int $perPage = 5): Paginator
    {
        $slice = $this->pageItems->slice($page->getCollectionIndex() - 1, $perPage + 1)->values();

        return new KickflipPaginator(
            $slice,
            $perPage,
            $page->getCollectionIndex(),
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ],
        );
    }
}
