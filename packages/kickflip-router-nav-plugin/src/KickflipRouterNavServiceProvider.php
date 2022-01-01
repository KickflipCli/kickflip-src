<?php

namespace Kickflip\RouterNavPlugin;

use Illuminate\Support\AggregateServiceProvider;

class KickflipRouterNavServiceProvider extends AggregateServiceProvider
{
    /**
     * The provider class names.
     *
     * @var array<class-string>
     */
    protected $providers = [
        RoutingServiceProvider::class,
        RoutingEventsServiceProvider::class,
    ];
}
