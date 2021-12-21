<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

beforeEach(function () {
    $buildPath = Str::of(\Kickflip\KickflipHelper::namedPath(\Kickflip\Enums\CliStateDirPaths::BuildDestination))->replaceEnv('local');
    if (is_dir($buildPath)) {
        File::deleteDirectory($buildPath);
    }
});

test('build command', function () {
    $this->artisan('build')
        ->assertExitCode(0);
});

test('test successful fake dirty build command', function () {
    $buildPath = Str::of(\Kickflip\KickflipHelper::namedPath(\Kickflip\Enums\CliStateDirPaths::BuildDestination))->replaceEnv('local');
    mkdir($buildPath);

    $this->artisan('build')
        ->expectsConfirmation('Overwrite "' . $buildPath . '"? ', 'yes')
        ->assertExitCode(0);
});

test('test denied fake dirty build command', function () {
    $buildPath = Str::of(\Kickflip\KickflipHelper::namedPath(\Kickflip\Enums\CliStateDirPaths::BuildDestination))->replaceEnv('local');
    mkdir($buildPath);

    $this->artisan('build')
        ->expectsConfirmation('Overwrite "' . $buildPath . '"? ', 'no')
        ->assertExitCode(1);
});