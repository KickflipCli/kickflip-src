<?php

// We do this to have access to laravel's filesystem facade used by SourcesLocator
use Kickflip\SiteBuilder\SiteBuilder;

uses(KickflipMonoTests\TestCase::class);

test('SiteBuilder can be constructed', function () {
    expect(SiteBuilder::class)->toBeString();
    expect(new SiteBuilder(false))
        ->toBeInstanceOf(SiteBuilder::class)
        ->toHaveProperties([
            'prettyUrls',
            'sourcesLocator',
        ]);
});
