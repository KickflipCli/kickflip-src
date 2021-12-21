<?php

use Illuminate\Support\Str;
use Kickflip\KickflipHelper;
use Kickflip\Models\NavItem;
use Kickflip\Models\PageInterface;

/**
 * Get the path to a versioned Mix file.
 *
 * @param  string  $path
 * @param  string  $manifestDirectory
 * @return \Illuminate\Support\HtmlString|string
 *
 * @throws \Exception
 */
function kickflip_asset_url($path)
{
    return KickflipHelper::assetUrl($path);
}

function relativeUrl(string $url): string
{
    return KickflipHelper::relativeUrl($url);
}


function isActive(PageInterface $page, string $path): bool
{
    return Str::endsWith(KickflipHelper::trimPath($page->getUrl()), KickflipHelper::trimPath($path));
}

function isActiveParent(PageInterface $page, NavItem $menuItem): bool
{
    $pageUrl = $page->getUrl();
    if ($menuItem->hasChildren()) {
        return collect($menuItem->children)->contains(function ($child) use ($pageUrl) {
            return KickflipHelper::trimPath($pageUrl) == KickflipHelper::trimPath($child->url);
        });
    }

    return false;
}

function getDocUrl(string $routeName, ?string $anchorLink = null): string
{
    $pageExists = static function(string $routeName) {
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
    };

    // Throw an exception if the routeName isn't a valid doc page...
    try {
        $routePagePath = $pageExists($routeName);
    } catch (\Throwable $throwable) {
        if (KickflipHelper::config('production')) {
            throw $throwable;
        }
        return '#link-error';
    }

    if ((null !== $anchorLink) && Str::contains(file_get_contents($routePagePath), sprintf('{#%s}', ltrim($anchorLink, '#')))) {
        if (KickflipHelper::config('prettyUrls')) {
            return sprintf('/docs/%s#%s', $routeName, ltrim($anchorLink, '#'));
        }
        return sprintf('/docs/%s.html#%s', $routeName, ltrim($anchorLink, '#'));
    }

    if (KickflipHelper::config('prettyUrls')) {
        return sprintf('/docs/%s', $routeName);
    }

    return sprintf('/docs/%s.html', $routeName);
}
