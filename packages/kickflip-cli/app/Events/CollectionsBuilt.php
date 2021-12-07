<?php

declare(strict_types=1);

namespace Kickflip\Events;

use Illuminate\Foundation\Events\Dispatchable;

class CollectionsBuilt extends BaseEvent
{
    use Dispatchable;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
}
