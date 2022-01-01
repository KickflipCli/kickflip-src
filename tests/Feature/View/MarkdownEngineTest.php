<?php

use Illuminate\View\View;
use Kickflip\Models\SiteData;
use Kickflip\View\Engine\MarkdownEngine;

it('can instantiate MarkdownEngine', function () {
    $markdownEngine = app(MarkdownEngine::class);
    expect($markdownEngine)->toBeInstanceOf(MarkdownEngine::class);
});

it('can make a view', function () {
    $mockSiteData = SiteData::fromConfig([
        'baseUrl' => 'http://example.com',
        'production' => true,
        'siteName' => 'Example Site',
        'siteDescription' => 'This is an example site.',
    ]);
    $mockPageData = getTestPageData(0);

    $data = [
        '__env' => app('view'),
        'app' => app(),
        'site' => $mockSiteData,
        'page' => $mockPageData,
    ];

    $markdownEngine = app(MarkdownEngine::class);
    expect($markdownEngine->get($mockPageData->source->getFullPath(), $data))->toBeString();
});

it('can make a non-extended view', function () {
    $mockSiteData = SiteData::fromConfig([
        'baseUrl' => 'http://example.com',
        'production' => true,
        'siteName' => 'Example Site',
        'siteDescription' => 'This is an example site.',
    ]);
    $mockPageData = getTestPageData(6);

    $data = [
        '__env' => app('view'),
        'app' => app(),
        'site' => $mockSiteData,
        'page' => $mockPageData,
    ];

    $markdownEngine = app(MarkdownEngine::class);
    expect($markdownEngine->get($mockPageData->source->getFullPath(), $data))->toBeString();
});
