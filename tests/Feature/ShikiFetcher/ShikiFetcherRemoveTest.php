<?php

use Kickflip\SiteBuilder\ShikiNpmFetcher;

beforeEach(function () {
    if (! (new ShikiNpmFetcher())->isShikiDownloaded()) {
        (new ShikiNpmFetcher())->installShiki();
    }
});

it('will remove shiki and node modules', function () {
    $shikiFetcher = new ShikiNpmFetcher();
    expect($shikiFetcher->getProjectRootDirectory() . '/package.json')
        ->toBeFile()->toBeReadableFile();
    expect($shikiFetcher->getProjectRootDirectory() . '/package-lock.json')
        ->toBeFile()->toBeReadableFile();
    expect($shikiFetcher->getProjectRootDirectory() . '/node_modules')
        ->toBeDirectory()->toBeWritableDirectory();
    $shikiFetcher->removeShikiAndNodeModules();
    expect($shikiFetcher->getProjectRootDirectory() . '/package.json')
        ->Not()->toBeFile();
    expect($shikiFetcher->getProjectRootDirectory() . '/package-lock.json')
        ->Not()->toBeFile()->Not()->toBeReadableFile();
    expect($shikiFetcher->getProjectRootDirectory() . '/node_modules')
        ->Not()->toBeDirectory()->Not()->toBeWritableDirectory();
});

it('can find shiki in dependencies or devDependencies', function () {
    $shikiFetcher = new ShikiNpmFetcher();
    $filePath = $shikiFetcher->getProjectRootDirectory() . '/package.json';
    expect($filePath)
        ->toBeFile();
    expect($shikiFetcher->isShikiRequired())->toBeTrue();
    file_put_contents($filePath, str_replace('devDependencies', 'dependencies', file_get_contents($filePath)));
    expect($shikiFetcher->isShikiRequired())->toBeTrue();
    file_put_contents($filePath, str_replace('dependencies', 'boogers', file_get_contents($filePath)));
    expect($shikiFetcher->isShikiRequired())->toBeFalse();
    $shikiFetcher->removeShikiAndNodeModules();
});
