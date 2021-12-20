<?php

use Kickflip\SiteBuilder\ShikiNpmFetcher;

it('can verify ShikiNpmFetcher exists', function () {
    expect(new ShikiNpmFetcher())->toBeInstanceOf(ShikiNpmFetcher::class);
});

it('can verify ShikiNpmFetcher methods', function () {
    $shikiFetcher = new ShikiNpmFetcher();
    expect($shikiFetcher)->toBeInstanceOf(ShikiNpmFetcher::class);
    expect($shikiFetcher->getProjectRootDirectory())
        ->toBeString()->toBe(dirname(__FILE__, 3));
    expect($shikiFetcher->isNpmUsedByProject())
        ->toBeBool();
    expect($shikiFetcher->isShikiRequired())
        ->toBeBool();
    expect($shikiFetcher->isShikiDownloaded())
        ->toBeBool();
});
