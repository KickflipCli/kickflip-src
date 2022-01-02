<?php

declare(strict_types=1);

namespace Kickflip\Events;

use Kickflip\Models\PageData;

final class PageDataCreated extends BaseEvent
{
    public function __construct(
        public PageData $pageData
    ) {
    }
}
