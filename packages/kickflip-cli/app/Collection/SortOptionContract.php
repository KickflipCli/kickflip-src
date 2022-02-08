<?php

declare(strict_types=1);

namespace Kickflip\Collection;

interface SortOptionContract
{
    public function toFilter(): callable;
}
