<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Kickflip\SiteBuilder\ShikiNpmFetcher;
use Symfony\Component\Process\Exception\ProcessFailedException;

afterEach(function () {
    (new ShikiNpmFetcher())->removeShikiAndNodeModules();
});
beforeEach(function () {
    (new ShikiNpmFetcher())->removeShikiAndNodeModules();
});

it('will throw an exception if shiki fetcher fails', function () {
    // Create shiki fetcher
    $shikiFetcher = new ShikiNpmFetcher();
    $nodeModules = $shikiFetcher->getProjectRootDirectory() . '/node_modules';
    $packageJson = $shikiFetcher->getProjectRootDirectory() . '/package.json';
    $packageLock = $shikiFetcher->getProjectRootDirectory() . '/package-lock.json';
    // Setup directory that will cause failure...
    mkdir($nodeModules, 0500);
    touch($packageJson);
    touch($packageLock);
    chmod($packageJson, 0400);
    chmod($packageLock, 0400);
    chmod($nodeModules, 0400);
    expect($nodeModules)->toBeDirectory();
    // Expect the exception and trigger the failure
    $this->expectException(ProcessFailedException::class);
    $shikiFetcher->installShiki();
    // Ensure 0500 perms directory is removed
    chmod($packageJson, 0700);
    chmod($packageLock, 0700);
    chmod($nodeModules, 0700);
    File::delete($packageLock);
    File::deleteDirectory($nodeModules);
    expect($nodeModules)->not->toBeDirectory();
});
