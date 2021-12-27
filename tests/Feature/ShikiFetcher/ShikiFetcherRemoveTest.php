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

it('will remove shiki and node modules', function () {
    $shikiFetcher = new ShikiNpmFetcher();
    if (! $shikiFetcher->isShikiDownloaded()) {
        $shikiFetcher->installShiki();
    }

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
    if (! $shikiFetcher->isShikiDownloaded()) {
        $shikiFetcher->installShiki();
    }

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
