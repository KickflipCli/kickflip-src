<?php

use Kickflip\SiteBuilder\ShikiNpmFetcher;

beforeEach(function () {
    if ((new ShikiNpmFetcher())->isShikiDownloaded()) {
        (new ShikiNpmFetcher())->removeShikiAndNodeModules();
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
