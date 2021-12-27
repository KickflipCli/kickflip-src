<?php

use Kickflip\SiteBuilder\ShikiNpmFetcher;

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

it('will remove shiki and node modules', function () {
    $shikiFetcher = new ShikiNpmFetcher();

    if ($shikiFetcher->isShikiDownloaded()) {
        $shikiFetcher->removeShikiAndNodeModules();
    }

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
