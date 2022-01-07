<?php

namespace Kickflip\SiteBuilder;

use BadFunctionCallException;
use DirectoryIterator;
use Illuminate\Support\Str;
use Kickflip\KickflipHelper;

final class UrlHelper
{
    /**
     * @param string $routeName
     * @return string
     * @throws BadFunctionCallException
     */
    public static function sourceFilePath(string $routeName): string
    {
        $sourceDocs = new DirectoryIterator(KickflipHelper::sourcePath('docs'));
        foreach ($sourceDocs as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            $fileName = Str::before($fileInfo->getFilename(), '.');

            if ($fileName === $routeName) {
                return $fileInfo->getPathname();
            }
        }

        throw new BadFunctionCallException("Cannot identify a page by the name: $routeName");
    }

    public static function getSourceFileUrl(string $routeName): string
    {
        /**
         * @var SourcesLocator $sourcesLocator
         */
        $sourcesLocator = app(SourcesLocator::class);
        $page = $sourcesLocator->getRenderPageByName($routeName);
        return KickflipHelper::rightTrimPath(KickflipHelper::config('baseUrl', '')) .
                    "/" .
                    KickflipHelper::leftTrimPath($page->getUrl());
    }
}
