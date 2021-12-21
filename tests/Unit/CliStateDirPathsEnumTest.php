<?php

use Kickflip\Enums\CliStateDirPaths;

test('verify CliStateDirPaths enum exists', function () {
    expect(class_exists(CliStateDirPaths::class))->toBeTrue();
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
    'BuildBase',
    'BuildSourcePart',
    'BuildSource',
    'BuildDestinationPart',
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
        'BuildBase',
        'build'
    ],
    [
        'BuildSourcePart',
        'source'
    ],
    [
        'BuildSource',
        'build.source'
    ],
    [
        'BuildDestinationPart',
        'destination'
    ],
    [
        'BuildDestination',
        'build.destination'
    ],
]);
