<?php

use Kickflip\Enums\CliStateDirPaths;

test('verify CliStateDirPaths enum exists', function () {
    expect(enum_exists(CliStateDirPaths::class))->toBeTrue();
});

test('verify CliStateDirPaths constants', function (string $constName) {
    expect(defined('Kickflip\Enums\CliStateDirPaths::' . $constName))->toBeTrue();
})->with([
    'Base',
    'Cache',
    'Resources',
    'Config',
    'EnvConfig',
    'BootstrapFile',
    'NavigationFile',
    'EnvNavigationFile',
    'BuildBase',
    'BuildSource',
    'BuildDestination',
]);

test('verify CliStateDirPaths constant values', function (string $constName, string $constValue) {
    expect(constant('Kickflip\Enums\CliStateDirPaths::' . $constName))->toBe($constValue);
})->with([
    [
        'Base',
        'baseDir'
    ],
    [
        'Cache',
        'cache'
    ],
    [
        'Resources',
        'resources'
    ],
    [
        'Config',
        'config'
    ],
    [
        'EnvConfig',
        'env_config'
    ],
    [
        'BootstrapFile',
        'bootstrapFile'
    ],
    [
        'NavigationFile',
        'navigationFile'
    ],
    [
        'EnvNavigationFile',
        'env_navigationFile'
    ],
    [
        'BuildBase',
        'build'
    ],
    [
        'BuildSource',
        'source'
    ],
    [
        'BuildDestination',
        'destination'
    ],
]);
