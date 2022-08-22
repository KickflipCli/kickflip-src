<?php

declare(strict_types=1);

namespace Kickflip\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Kickflip\Events\PageDataCreated;
use Kickflip\Events\SiteBuildStarted;
use Kickflip\Listeners\BuildSiteNavRoute;
use Kickflip\Listeners\FindSources;
use Kickflip\Listeners\SetupSiteNav;

class EventsServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        PageDataCreated::class => [
            BuildSiteNavRoute::class,
        ],
        SiteBuildStarted::class => [
            FindSources::class,
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
    }
}
