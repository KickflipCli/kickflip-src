<?php

declare(strict_types=1);

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Kickflip\KickflipHelper;
use Kickflip\Models\PageInterface;
use Kickflip\RouterNavPlugin\Models\NavItem;
use Kickflip\SiteBuilder\UrlHelper;

// phpcs:disable
/**
 * Get the path to a versioned Mix file.
 *
 * @param string $manifestDirectory
 *
 * @return HtmlString|string
 *
 * @throws Exception
 */
function kickflip_asset_url(string $path)
{
    return KickflipHelper::assetUrl($path);
}

function relativeUrl(string $url): string
{
    return KickflipHelper::relativeUrl($url);
}

function isActive(PageInterface $currentPage, string $navItemUrl): bool
{
    return Str::endsWith(
        KickflipHelper::trimPath($currentPage->getUrl()),
        KickflipHelper::trimPath(parse_url($navItemUrl, PHP_URL_PATH) ?? '')
    );
}

function isActiveParent(PageInterface $page, NavItem $menuItem): bool
{
    $pageUrl = $page->getUrl();
    if ($menuItem->hasChildren()) {
        return collect($menuItem->children)
            ->filter(static fn ($value) => !str_starts_with($value->url, '#')) // Remove Anchor links
            ->map(static fn ($value) => parse_url($value->url, PHP_URL_PATH)) // Map every URL to just paths...
            ->contains(fn ($childUrl) => KickflipHelper::trimPath($pageUrl) === KickflipHelper::trimPath($childUrl));
    }

    return false;
}

function getDocUrl(string $routeName, ?string $anchorLink = null): string
{
    // Throw an exception if the routeName isn't a valid doc page...
    try {
        $pageUrl = UrlHelper::getSourceFileUrl('docs.' . $routeName);
    } catch (Throwable $throwable) {
        if (KickflipHelper::config('production', false)) {
            throw $throwable;
        }

        return '#link-error';
    }

    if (
        $anchorLink !== null &&
            str_starts_with($anchorLink, '#') ||
        (
            $anchorLink !== null &&
            true // TODO: Figure out the other condition here...
        )
    ) {
        return sprintf('%s#%s', $pageUrl, ltrim($anchorLink, '#'));
    }

    return $pageUrl;
}
