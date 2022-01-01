<?php

declare(strict_types=1);

namespace Kickflip\RouterNavPlugin;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Kickflip\Events\PageDataCreated;
use Kickflip\Events\SiteBuildStarted;
use Kickflip\RouterNavPlugin\Listeners\BuildSiteNavRoutes;
use Kickflip\RouterNavPlugin\Listeners\SetupSiteNav;

class RoutingEventsServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        PageDataCreated::class => [
            BuildSiteNavRoutes::class,
        ],
        SiteBuildStarted::class => [
            SetupSiteNav::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
