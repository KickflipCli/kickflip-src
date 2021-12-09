<?php

use Kickflip\Models\PageData;
use Kickflip\SourcesLocator;

// We do this to have access to laravel's filesystem facade used by SourcesLocator
uses(KickflipTests\TestCase::class);

test('SourcesLocator can be constructed', function () {
    expect(SourcesLocator::class)->toBeString();
    expect(new SourcesLocator(dirname(__DIR__) . '/sources'))
        ->toBeInstanceOf(SourcesLocator::class)
        ->toHaveProperties([
            'sourcesBasePath',
            'renderPageList',
            'bladeSources',
            'markdownSources',
            'markdownBladeSources',
        ]);
});

test('SourcesLocator has expected properties', function () {
    expect(new SourcesLocator(dirname(__DIR__) . '/sources'))
        ->toHaveProperties([
            'sourcesBasePath',
            'renderPageList',
            'bladeSources',
            'markdownSources',
            'markdownBladeSources',
        ]);
});

test('SourcesLocator has expected methods', function () {
    $sourcesLocator = new SourcesLocator(dirname(__DIR__) . '/sources');
    expect($sourcesLocator->getRenderPageList())
        ->toBeArray()
        ->toHaveCount(5)
        ->each->toBeInstanceOf(PageData::class);
});
