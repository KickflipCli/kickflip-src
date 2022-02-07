<?php

declare(strict_types=1);

namespace Kickflip\Collection;

use Illuminate\Support\Collection;
use Kickflip\Models\PageData;

use function collect;
use function gettype;
use function strcmp;

class SortHandler
{
    public function __construct(
        private CollectionConfig $config,
    ) {
    }

    /**
     * @param array<PageData> $items
     */
    public function __invoke(array $items): Collection
    {
        $collection = collect($items);
        foreach ($this->config->sort as $sortOption) {
            $collection = $collection->sort(callback: static function ($itemA, $itemB) use ($sortOption) {
                $sortValueA = $sortOption->toFilter()($itemA);
                $sortValueB = $sortOption->toFilter()($itemB);

                return match (gettype($sortValueA)) {
                    'integer', 'double' => $sortValueA <=> $sortValueB,
                    'string' => strcmp($sortValueA, $sortValueB),
                };
            });
        }
        // Reset keys to new order and then update PageData
        $collection = $collection->values();
        $collection->each(static fn (PageData $value, int $key) => $value->updateCollectionIndex($key));

        return $collection;
    }
}
