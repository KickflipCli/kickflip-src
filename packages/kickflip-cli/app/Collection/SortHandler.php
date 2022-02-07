<?php

declare(strict_types=1);

namespace Kickflip\Collection;

use Kickflip\Models\PageData;

use function collect;
use function strcmp;

class SortHandler
{
    public function __construct(
        private CollectionConfig $config,
    ) {
    }

    /**
     * @param array<PageData> $items
     *
     * @return array<PageData>
     */
    public function __invoke(array $items): array
    {
        $collection = collect($items);
        foreach ($this->config->sort as $sortOption) {
            $collection = $collection->sort(fn ($itemA, $itemB) => strcmp($sortOption->toFilter()($itemA), $sortOption->toFilter()($itemB)));
        }

        return $collection->values()->toArray();
    }
}
