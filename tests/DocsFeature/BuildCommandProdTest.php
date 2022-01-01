<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Kickflip\SiteBuilder\ShikiNpmFetcher;

afterEach(function () {
    (new ShikiNpmFetcher())->removeShikiAndNodeModules();
    $buildPath = Str::of(\Kickflip\KickflipHelper::namedPath(\Kickflip\Enums\CliStateDirPaths::BuildDestination))->replaceEnv('production');
    if (is_dir($buildPath)) {
        File::deleteDirectory($buildPath);
    }
});
beforeEach(function () {
    (new ShikiNpmFetcher())->removeShikiAndNodeModules();
    $buildPath = Str::of(\Kickflip\KickflipHelper::namedPath(\Kickflip\Enums\CliStateDirPaths::BuildDestination))->replaceEnv('production');
    if (is_dir($buildPath)) {
        File::deleteDirectory($buildPath);
    }
});

test('build command', function () {
    $this->artisan('build production')
        ->assertExitCode(0);
});

test('test successful fake dirty build command', function () {
    $buildPath = Str::of(\Kickflip\KickflipHelper::namedPath(\Kickflip\Enums\CliStateDirPaths::BuildDestination))->replaceEnv('production');
    mkdir($buildPath);

    $this->artisan('build production')
        ->expectsConfirmation('Overwrite "' . $buildPath . '"? ', 'yes')
        ->assertExitCode(0);
});

test('test denied fake dirty build command', function () {
    $buildPath = Str::of(\Kickflip\KickflipHelper::namedPath(\Kickflip\Enums\CliStateDirPaths::BuildDestination))->replaceEnv('production');
    mkdir($buildPath);
    $this->artisan('build production')
        ->expectsConfirmation('Overwrite "' . $buildPath . '"? ', 'no')
        ->assertExitCode(1);
});
