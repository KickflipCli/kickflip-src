<?php

use Kickflip\SiteBuilder\ShikiNpmFetcher;

beforeEach(function () {
    $shikiFetcher = new ShikiNpmFetcher();
    if ($shikiFetcher->isShikiDownloaded()) {
        $shikiFetcher->removeShikiAndNodeModules();
    }
    $nodeModules = $shikiFetcher->getProjectRootDirectory() . '/node_modules';
    if (is_dir($nodeModules)) {
        rmdir($nodeModules);
    }
});

afterEach(function () {
    $shikiFetcher = new ShikiNpmFetcher();
    if ($shikiFetcher->isShikiDownloaded()) {
        $shikiFetcher->removeShikiAndNodeModules();
    }
    $nodeModules = $shikiFetcher->getProjectRootDirectory() . '/node_modules';
    if (is_dir($nodeModules)) {
        rmdir($nodeModules);
    }
});

it('will throw an exception if shiki fetcher fails', function () {
    $shikiFetcher = new ShikiNpmFetcher();
    $nodeModules = $shikiFetcher->getProjectRootDirectory() . '/node_modules';
    mkdir($nodeModules, '0500');
    chmod($nodeModules, 0500);
    $this->expectException(\Symfony\Component\Process\Exception\ProcessFailedException::class);
    $shikiFetcher->installShiki();
});
