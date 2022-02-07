<?php

declare(strict_types=1);

use Illuminate\Support\HtmlString;
use Kickflip\KickflipHelper;

if (!function_exists('kickflip_asset_url')) {
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
}


if (!function_exists('relativeUrl')) {
    function relativeUrl(string $url): string
    {
        return KickflipHelper::relativeUrl($url);
    }
}
