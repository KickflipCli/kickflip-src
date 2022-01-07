<?php

declare(strict_types=1);

namespace Kickflip\SiteBuilder;

use BadFunctionCallException;
use Kickflip\KickflipHelper;
use Kickflip\Models\PageData;

use function app;

final class UrlHelper
{
    /**
     * @throws BadFunctionCallException
     */
    public static function sourceFilePath(string $routeName): string
    {
        /**
         * @var SourcesLocator $sourcesLocator
         */
        $sourcesLocator = app(SourcesLocator::class);
        $page = $sourcesLocator->getRenderPageByName($routeName);

        return $page->source->getFullPath();
    }

    public static function getSourceFileUrl(string $routeName): string
    {
        /**
         * @var SourcesLocator $sourcesLocator
         */
        $sourcesLocator = app(SourcesLocator::class);
        $page = $sourcesLocator->getRenderPageByName($routeName);

        return KickflipHelper::rightTrimPath(KickflipHelper::config('site.baseUrl', '')) .
                    '/' .
                    KickflipHelper::leftTrimPath($page->getUrl());
    }

    public static function getPageUrl(PageData $page): string
    {
        $baseUrl = KickflipHelper::config('site.baseUrl', '');
        return KickflipHelper::rightTrimPath($baseUrl) .
            '/' .
            KickflipHelper::leftTrimPath($page->getUrl());
    }
}
