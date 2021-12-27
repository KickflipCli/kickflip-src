<?php

use Illuminate\Support\Facades\File;
use Kickflip\SiteBuilder\ShikiNpmFetcher;

afterEach(function () {
    $shikiFetcher = new ShikiNpmFetcher();
    if ($shikiFetcher->isShikiDownloaded()) {
        $shikiFetcher->removeShikiAndNodeModules();
    }
    $nodeModules = $shikiFetcher->getProjectRootDirectory() . '/node_modules';
    if (File::isDirectory($nodeModules)) {
        File::delete($shikiFetcher->getProjectRootDirectory() . '/package.json');
        File::delete($shikiFetcher->getProjectRootDirectory() . '/package-lock.json');
        File::deleteDirectory($nodeModules);
    }
});

beforeEach(function () {
    $shikiFetcher = new ShikiNpmFetcher();
    if ($shikiFetcher->isShikiDownloaded()) {
        $shikiFetcher->removeShikiAndNodeModules();
    }
    $nodeModules = $shikiFetcher->getProjectRootDirectory() . '/node_modules';
    if (File::isDirectory($nodeModules)) {
        File::delete($shikiFetcher->getProjectRootDirectory() . '/package.json');
        File::delete($shikiFetcher->getProjectRootDirectory() . '/package-lock.json');
        File::deleteDirectory($nodeModules);
    }
});

it('will remove shiki and node modules', function () {
    $shikiFetcher = new ShikiNpmFetcher();
    expect($shikiFetcher->getProjectRootDirectory() . '/package.json')
        ->Not()->toBeFile();
    expect($shikiFetcher->getProjectRootDirectory() . '/package-lock.json')
        ->Not()->toBeFile()->Not()->toBeReadableFile();
    expect($shikiFetcher->getProjectRootDirectory() . '/node_modules')
        ->Not()->toBeDirectory()->Not()->toBeWritableDirectory();

    $shikiFetcher->installShiki();

    expect($shikiFetcher->getProjectRootDirectory() . '/package.json')
        ->toBeFile()->toBeReadableFile();
    expect($shikiFetcher->getProjectRootDirectory() . '/package-lock.json')
        ->toBeFile()->toBeReadableFile();
    expect($shikiFetcher->getProjectRootDirectory() . '/node_modules')
        ->toBeDirectory()->toBeWritableDirectory();
});
