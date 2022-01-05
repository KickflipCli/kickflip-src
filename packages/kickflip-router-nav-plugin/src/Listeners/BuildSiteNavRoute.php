<?php

declare(strict_types=1);

namespace Kickflip\RouterNavPlugin\Listeners;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Kickflip\Events\PageDataCreated;
use Kickflip\KickflipHelper;

use function parse_url;

use const PHP_URL_PATH;

/**
 * An event handler that will listen for PageDataCreated events.
 *
 * This listener will determine the appropriate route name from a PageData's URL,
 * then registers a faux GET route with laravel's router.
 */
class BuildSiteNavRoute
{
    public function handle(PageDataCreated $event)
    {
        $baseDirectory = parse_url(KickflipHelper::config('site.baseUrl'), PHP_URL_PATH);
        $pageData = $event->pageData;
        $url = $pageData->getUrl();

        // Ensure index route has index name...
        if ($url === '/') {
            $routeName = 'index';
        } else {
            $routeName = (string) Str::of($url)->trim('/')->replace('/', '.');
        }

        // Register a thin route based on the file name...
        if ($baseDirectory !== null) {
            Route::name($routeName)->prefix($baseDirectory)->get($pageData->getUrl(), static fn () => '');
        } else {
            Route::name($routeName)->get($pageData->getUrl(), static fn () => '');
        }
    }
}
