<?php

declare(strict_types=1);

namespace Kickflip\RouterNavPlugin\Listeners;

use Illuminate\Support\Facades\Route;
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
final class BuildSiteNavRoute
{
    public function handle(PageDataCreated $event)
    {
        // Determine if site config specifies baseUrl with subdirectory path
        $baseDirectory = parse_url(
            KickflipHelper::config('site.baseUrl', ''),
            PHP_URL_PATH,
        );
        // Register a thin route based on the file name...
        if ($baseDirectory !== null && $baseDirectory !== '/') {
            Route::name(KickflipHelper::pageRouteName($event->pageData))
                ->prefix($baseDirectory)
                ->get($event->pageData->getUrl(), static fn () => '');
        } else {
            Route::name(KickflipHelper::pageRouteName($event->pageData))
                ->get($event->pageData->getUrl(), static fn () => '');
        }
    }
}
