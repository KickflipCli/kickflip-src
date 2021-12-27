<?php

use Illuminate\Support\Facades\File;
use Kickflip\SiteBuilder\ShikiNpmFetcher;

beforeEach(function () {
    $shikiFetcher = new ShikiNpmFetcher();
    if ($shikiFetcher->isShikiDownloaded()) {
        $shikiFetcher->removeShikiAndNodeModules();
    }
    $nodeModules = $shikiFetcher->getProjectRootDirectory() . '/node_modules';
    if (File::isDirectory($nodeModules)) {
        File::deleteDirectory($nodeModules);
    }
});

afterEach(function () {
    $shikiFetcher = new ShikiNpmFetcher();
    if ($shikiFetcher->isShikiDownloaded()) {
        $shikiFetcher->removeShikiAndNodeModules();
    }
    $nodeModules = $shikiFetcher->getProjectRootDirectory() . '/node_modules';
    if (File::isDirectory($nodeModules)) {
        File::deleteDirectory($nodeModules);
    }
});

it('will throw an exception if shiki fetcher fails', function () {
    // Create shiki fetcher
    $shikiFetcher = new ShikiNpmFetcher();
    $nodeModules = $shikiFetcher->getProjectRootDirectory() . '/node_modules';
    // Setup directory that will cause failure...
    mkdir($nodeModules, '0500');
    chmod($nodeModules, 0500);
    expect($nodeModules)->toBeDirectory();
    // Expect the exception and trigger the failure
    $this->expectException(\Symfony\Component\Process\Exception\ProcessFailedException::class);
    $shikiFetcher->installShiki();
    // Ensure 0500 perms directory is removed
    chmod($nodeModules, 0700);
    File::deleteDirectory($nodeModules);
    expect($nodeModules)->not->toBeDirectory();
});
