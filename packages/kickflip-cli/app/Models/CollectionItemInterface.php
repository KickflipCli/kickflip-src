<?php

declare(strict_types=1);

namespace Kickflip\Models;

use Illuminate\Support\Collection;

interface CollectionItemInterface extends PageInterface
{
    public function getCollection(): Collection;

    public function getPrevious(): CollectionItemInterface;

    public function getNext(): CollectionItemInterface;

    public function getFirst(): CollectionItemInterface;

    public function getLast(): CollectionItemInterface;
}
