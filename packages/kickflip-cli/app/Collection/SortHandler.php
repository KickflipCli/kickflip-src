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
        /**
         * @var SortOptionContract $sortOption
         */
        foreach ($this->config->sort as $sortOption) {
            $collection = $collection->sort(callback: static function ($itemA, $itemB) use ($sortOption) {
                $isInverted = ($sortOption::class === InverseSortOption::class);
                $sortFilter = $sortOption->toFilter();
                $sortValueA = $sortFilter($itemA);
                $sortValueB = $sortFilter($itemB);

                $type = gettype($sortValueA);
                if ($type === 'integer' || $type === 'double') {
                    if ($isInverted) {
                        return $sortValueB <=> $sortValueA;
                    }

                    return $sortValueA <=> $sortValueB;
                }

                if ($isInverted) {
                    return strcmp($sortValueB, $sortValueA);
                }

                return strcmp($sortValueA, $sortValueB);
            });
        }
        // Reset keys to new order and then update PageData
        $collection = $collection->values();
        $collection->each(static fn (PageData $value, int $key) => $value->updateCollectionIndex($key));

        return $collection;
    }
}
