<?php

use Kickflip\Models\PageData;
use Kickflip\SiteBuilder\SourcesLocator;

// We do this to have access to laravel's filesystem facade used by SourcesLocator
uses(KickflipMonoTests\TestCase::class);

test('SourcesLocator class exists', function () {
    expect(SourcesLocator::class)->toBeString();
});

test('SourcesLocator can be constructed', function () {
    expect(new SourcesLocator(dirname(__DIR__, 2) . '/sources'))
        ->toBeInstanceOf(SourcesLocator::class);
});

test('SourcesLocator has expected properties', function () {
    expect(new SourcesLocator(dirname(__DIR__, 2) . '/sources'))
        ->toHaveProperties([
            'sourcesBasePath',
            'renderPageList',
            'bladeSources',
            'markdownSources',
            'markdownBladeSources',
        ]);
});

test('SourcesLocator has expected methods', function () {
    $sourcesLocator = new SourcesLocator(dirname(__DIR__, 2) . '/sources');
    expect($sourcesLocator->getRenderPageList())
        ->toBeArray()
        ->toHaveCount(7)
        ->each->toBeInstanceOf(PageData::class);
});
