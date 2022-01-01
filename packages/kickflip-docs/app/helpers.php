<?php

use Illuminate\Support\Str;
use Kickflip\KickflipHelper;
use Kickflip\Models\PageInterface;
use Kickflip\RouterNavPlugin\Models\NavItem;

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


function isActive(PageInterface $currentPage, string $navItemUrl): bool
{
    return Str::endsWith(KickflipHelper::trimPath($currentPage->getUrl()), KickflipHelper::trimPath(parse_url($navItemUrl, PHP_URL_PATH) ?? ''));
}

function isActiveParent(PageInterface $page, NavItem $menuItem): bool
{
    $pageUrl = $page->getUrl();
    if ($menuItem->hasChildren()) {
        return collect($menuItem->children)
            ->filter(static fn($value) => !str_starts_with($value->url, '#')) // Remove Anchor links
            ->map(static fn($value) => parse_url($value->url, PHP_URL_PATH)) // Map every URL to just paths...
            ->contains(function ($childUrl) use ($pageUrl) {
                return KickflipHelper::trimPath($pageUrl) === KickflipHelper::trimPath($childUrl);
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

    if (
        null !== $anchorLink &&
        Str::contains(file_get_contents($routePagePath), sprintf('{#%s}', ltrim($anchorLink, '#')))
    ) {
        return sprintf('%s#%s', route('docs.'.$routeName), ltrim($anchorLink, '#'));
    }

    return route('docs.'.$routeName);
}
