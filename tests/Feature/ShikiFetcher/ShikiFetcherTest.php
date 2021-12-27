<?php

use Illuminate\Support\Facades\File;
use Kickflip\SiteBuilder\ShikiNpmFetcher;

afterEach(function () {
    (new ShikiNpmFetcher())->removeShikiAndNodeModules();
});
beforeEach(function () {
    (new ShikiNpmFetcher())->removeShikiAndNodeModules();
});

it('can verify ShikiNpmFetcher exists', function () {
    expect(new ShikiNpmFetcher())->toBeInstanceOf(ShikiNpmFetcher::class);
});

it('can verify ShikiNpmFetcher methods', function () {
    $shikiFetcher = new ShikiNpmFetcher();
    expect($shikiFetcher)->toBeInstanceOf(ShikiNpmFetcher::class);
    expect($shikiFetcher->getProjectRootDirectory())
        ->toBeString()->toBe(dirname(__FILE__, 4));
    expect($shikiFetcher->isNpmUsedByProject())
        ->toBeBool()->toBeFalse();
    expect($shikiFetcher->isShikiRequired())
        ->toBeBool()->toBeFalse();
    expect($shikiFetcher->isShikiDownloaded())
        ->toBeBool()->toBeFalse();
});

it('can verify ShikiNpmFetcher methods when installed', function () {
    $shikiFetcher = new ShikiNpmFetcher();
    $shikiFetcher->installShiki();
    expect($shikiFetcher)->toBeInstanceOf(ShikiNpmFetcher::class);
    expect($shikiFetcher->getProjectRootDirectory())
        ->toBeString()->toBe(dirname(__FILE__, 4));
    expect($shikiFetcher->isNpmUsedByProject())
        ->toBeBool()->toBeFalse();
    expect($shikiFetcher->isShikiRequired())
        ->toBeBool()->toBeTrue();
    expect($shikiFetcher->isShikiDownloaded())
        ->toBeBool()->toBeTrue();
});

it('can reproduce a bug in GH actions', function () {
    // Initialize shiki npm state when bug happens...
    $shikiFetcher = new ShikiNpmFetcher();
    $shikiFetcher->installShiki();
    $rootPath = $shikiFetcher->getProjectRootDirectory();
    unset($shikiFetcher);
    if (File::isFile($rootPath . '/package.json')) {
        File::delete($rootPath . '/package.json');
    }

    // After file env matches the bug create a new fetcher
    $shikiFetcher = new ShikiNpmFetcher();
    expect($shikiFetcher->isNpmUsedByProject())
        ->toBeBool()->toBeFalse();
    expect($shikiFetcher->isShikiRequired())
        ->toBeBool()->toBeTrue();
    expect($shikiFetcher->isShikiRequiredPackage())
        ->toBeBool()->toBeFalse();
    expect($shikiFetcher->isShikiRequiredPackageLock())
        ->toBeBool()->toBeTrue();
    expect($shikiFetcher->isShikiDownloaded())
        ->toBeBool()->toBeTrue();
});
