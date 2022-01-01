<?php

declare(strict_types=1);

namespace KickflipDocs\Listeners;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Kickflip\Events\PageDataCreated;
use Kickflip\Models\PageData;

class BuildSiteNavRoutes
{
    public function handle(PageDataCreated $event)
    {
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
