<?php

declare(strict_types=1);

namespace Kickflip\View;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class KickflipPaginator extends Paginator
{
    /**
     * Set the items for the paginator.
     *
     * @return void
     */
    protected function setItems(mixed $items)
    {
        $this->items = $items instanceof Collection ? $items : Collection::make($items);

        $this->hasMore = $this->items->count() > $this->perPage;

        $this->items = $this->items->slice(0, $this->perPage);
    }

    /**
     * Get the URL for a given page number.
     *
     * @return string
     */
    public function url(int $page)
    {
        if ($page <= 0) {
            $page = 1;
        }

        $paginatorCollection = $this->items[0]->getCollection();

        return $paginatorCollection->get($page - 1)?->getUrl();
    }

    /**
     * Get the URL for the next page.
     */
    public function nextPageUrl(): ?string
    {
        if ($this->hasMorePages()) {
            return $this->url($this->currentPage() + 1);
        }

        return null;
    }

    /**
     * Get the URL for the previous page.
     */
    public function previousPageUrl(): ?string
    {
        if ($this->currentPage() > 1) {
            return $this->url($this->currentPage() - 1);
        }

        return null;
    }
}
