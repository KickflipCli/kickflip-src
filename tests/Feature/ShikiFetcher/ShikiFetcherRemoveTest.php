<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Kickflip\SiteBuilder\ShikiNpmFetcher;

afterEach(function () {
    (new ShikiNpmFetcher())->removeShikiAndNodeModules();
});
beforeEach(function () {
    (new ShikiNpmFetcher())->installShiki();
});

it('will remove shiki and node modules', function () {
    $shikiFetcher = new ShikiNpmFetcher();
    expect($shikiFetcher->getProjectRootDirectory() . '/package.json')
        ->when(filter_var(Str::of(getNodeVersion())->before('.')->after('v'), FILTER_VALIDATE_INT) >= 15, fn($path) => $path->toBeFile()->toBeReadableFile());
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
    if (!File::exists($filePath)) {
        $filePath = $shikiFetcher->getProjectRootDirectory() . '/package-lock.json';
    } else {
        File::delete($shikiFetcher->getProjectRootDirectory() . '/package-lock.json');
    }
    expect($filePath)->toBeFile()->toBeReadableFile();
    expect($shikiFetcher->isShikiRequired())->toBeTrue();
    // Change devDeps to deps...
    file_put_contents($filePath, str_replace('devDependencies', 'dependencies', file_get_contents($filePath)));
    expect($shikiFetcher->isShikiRequired())->toBeTrue();
    // Change deps to boogers...
    file_put_contents($filePath, str_replace('dependencies', 'boogers', file_get_contents($filePath)));
    expect($shikiFetcher->isShikiRequired())->toBeFalse();
});
