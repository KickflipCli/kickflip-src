<?php

declare(strict_types=1);

namespace Kickflip\RouterNavPlugin\Listeners;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Kickflip\Events\PageDataCreated;
use Kickflip\Models\PageData;

/**
 * An event handler that will listen for PageDataCreated events.
 *
 * This listener will determine the appropriate route name from a PageData's URL, then registers a faux GET route with laravels router.
 */
class BuildSiteNavRoutes
{
    public function handle(PageDataCreated $event)
    {
        /**
         * @var PageData $pageData
         */
        $pageData = $event->pageData;
        $url = $pageData->getUrl();
        # Register thin route for URL generating...
        if ($url === '/') {
            $routeName = 'index';
        } else {
            $routeName = (string) Str::of($url)->trim('/')->replace('/', '.');
        }
        Route::name($routeName)->get($pageData->getUrl(), static fn() => '');
    }
}
