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

use function array_slice;
use function collect;
use function dirname;
use function is_dir;
use function iterator_to_array;

/**
 * @protected array<PageData> $items
 */
class PageCollection extends Collection
{
    public string $name;
    public CollectionConfig $config;
    /**
     * @var Collection<SourcePageMetaData>
     */
    public Collection $sourceItems;

    public function __construct(CollectionConfig $config, $items = [])
    {
        $this->name = $config->name;
        $this->config = $config;
        $this->sourceItems = collect();

        if (is_dir($config->basePath)) {
            $collectionSourceFiles = collect(iterator_to_array(
                Finder::create()
                    ->files()
                    ->ignoreDotFiles(true)
                    ->in(dirname($config->basePath))
                    ->sortByName(),
                false,
            ))
                ->reject(static fn (SplFileInfo $value) => ! Str::of($value->getRelativePath())->startsWith($config->path))
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
        parent::__construct($items);
    }

    public static function fromConfig(CollectionConfig $config): PageCollection
    {
        return new PageCollection($config);
    }

    /**
     * @param array<PageData> $collectionItems
     */
    public function loadItems(array $collectionItems, array &$renderPageList)
    {
        $this->items = $collectionItems = (new SortHandler($this->config))($collectionItems);
        foreach ($collectionItems as $key => $collectionItem) {
            $collectionItem->updateCollectionIndex($key);
            $renderPageList[] = $collectionItem;
        }
    }

    public function backAndNextPaginate(PageData $page): Paginator
    {
        return new KickflipPaginator(
            array_slice($this->items, $page->getCollectionIndex() - 1, 2),
            1,
            $page->getCollectionIndex(),
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ],
        );
    }

    public function paginate(PageData $page, $perPage = 5): Paginator
    {
        return new KickflipPaginator(
            array_slice($this->items, $page->getCollectionIndex() - 1, $perPage + 1),
            $perPage,
            $page->getCollectionIndex(),
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ],
        );
    }
}
