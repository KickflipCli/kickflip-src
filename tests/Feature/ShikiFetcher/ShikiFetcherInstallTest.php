<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Kickflip\SiteBuilder\ShikiNpmFetcher;

afterEach(function () {
    (new ShikiNpmFetcher())->removeShikiAndNodeModules();
});
beforeEach(function () {
    (new ShikiNpmFetcher())->removeShikiAndNodeModules();
});

it('will install shiki and node modules', function () {
    $shikiFetcher = new ShikiNpmFetcher();
    expect($shikiFetcher->getProjectRootDirectory() . '/package.json')
        ->Not()->toBeFile();
    expect($shikiFetcher->getProjectRootDirectory() . '/package-lock.json')
        ->Not()->toBeFile()->Not()->toBeReadableFile();
    expect($shikiFetcher->getProjectRootDirectory() . '/node_modules')
        ->Not()->toBeDirectory()->Not()->toBeWritableDirectory();

    $shikiFetcher->installShiki();

    expect($shikiFetcher->getProjectRootDirectory() . '/package.json')
        ->when(filter_var(Str::of(getNodeVersion())->before('.')->after('v'), FILTER_VALIDATE_INT) >= 15, fn($path) => $path->toBeFile()->toBeReadableFile());
    expect($shikiFetcher->getProjectRootDirectory() . '/package-lock.json')
        ->toBeFile()->toBeReadableFile();
    expect($shikiFetcher->getProjectRootDirectory() . '/node_modules')
        ->toBeDirectory()->toBeWritableDirectory();
});
