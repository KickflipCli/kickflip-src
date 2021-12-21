<?php

use Kickflip\Models\SiteData;
use Kickflip\View\Engine\BladeMarkdownEngine;

it('can instantiate BladeMarkdownEngine', function () {
    $bladeMarkdownEngine = app(BladeMarkdownEngine::class);
    expect($bladeMarkdownEngine)->toBeInstanceOf(BladeMarkdownEngine::class);
});

it('can make a view', function () {
    $mockSiteData = SiteData::fromConfig([
        'baseUrl' => 'http://example.com',
        'production' => true,
        'siteName' => 'Example Site',
        'siteDescription' => 'This is an example site.',
    ], []);
    $mockPageData = getTestPageData(1);
    $data = [
        '__env' => app('view'),
        'app' => app(),
        'site' => $mockSiteData,
        'page' => $mockPageData,
    ];

    $bladeMarkdownEngine = app(BladeMarkdownEngine::class);
    expect($bladeMarkdownEngine->get($mockPageData->source->getFullPath(), $data))->toBeString();
});

it('can make a non-extended view', function () {
    $mockSiteData = SiteData::fromConfig([
        'baseUrl' => 'http://example.com',
        'production' => true,
        'siteName' => 'Example Site',
        'siteDescription' => 'This is an example site.',
    ], []);
    $mockPageData = getTestPageData(7);

    $data = [
        '__env' => app('view'),
        'app' => app(),
        'site' => $mockSiteData,
        'page' => $mockPageData,
    ];

    $bladeMarkdownEngine = app(BladeMarkdownEngine::class);
    expect($bladeMarkdownEngine->get($mockPageData->source->getFullPath(), $data))->toBeString();
});
