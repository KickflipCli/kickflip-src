<?php

use Kickflip\KickflipHelper;

if (!function_exists('kickflip_asset_url')) {
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
}


if (!function_exists('relativeUrl')) {
    function relativeUrl(string $url): string
    {
        return KickflipHelper::relativeUrl($url);
    }
}
