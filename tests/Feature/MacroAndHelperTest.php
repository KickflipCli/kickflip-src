<?php

declare(strict_types=1);

use Illuminate\Support\Str;
use Kickflip\Enums\CliStateDirPaths;
use Kickflip\KickflipHelper;

test('string macro replaceEnv', function ($input, $expected) {
    $stringFormat = Str::of('Hello, {env}');
    expect((string) $stringFormat->replaceEnv($input))
        ->toBeString()
        ->toBe($expected);
})->with([
    ['world', 'Hello, world'],
    ['space', 'Hello, space'],
    ['Laravel', 'Hello, Laravel'],
    ['PHP', 'Hello, PHP'],
]);

test('KickflipHelper::config', function ($input, $expected) {
    expect(KickflipHelper::config('paths.' . $input))
        ->toBeString()->toBe(dirname(__DIR__, 2) . $expected);
})->with([
    ['baseDir', '/packages/kickflip'],
    ['cache', '/packages/kickflip/cache'],
    ['env_config', '/packages/kickflip/config/config.{env}.php'],
]);

test('KickflipHelper::namedPath', function ($input, $expected) {
    expect(KickflipHelper::namedPath($input))
        ->toBeString()
        ->toBe(dirname(__DIR__, 2) . $expected);
})->with([
    [CliStateDirPaths::Base, '/packages/kickflip'],
    [CliStateDirPaths::ConfigFile, '/packages/kickflip/config/config.php'],
    [CliStateDirPaths::BootstrapFile, '/packages/kickflip/config/bootstrap.php'],
]);

test('KickflipHelper::assetUrl', function ($input, $expected) {
    expect(KickflipHelper::assetUrl($input))
        ->isHtmlStringOf($expected);
})->with([
    ['', 'http://kickflip.test/assets/'],
    ['blue', 'http://kickflip.test/assets/blue'],
    ['hello/world', 'http://kickflip.test/assets/hello/world'],
]);

test('KickflipHelper::resourcePath', function ($input, $expected) {
    expect(KickflipHelper::resourcePath($input))
        ->toBeString()
        ->toBe(dirname(__DIR__, 2) . $expected);
})->with([
    ['', '/packages/kickflip/resources'],
    ['blue', '/packages/kickflip/resources/blue'],
    ['hello/world', '/packages/kickflip/resources/hello/world'],
]);

test('KickflipHelper::sourcePath', function ($input, $expected) {
    expect(KickflipHelper::sourcePath($input))
        ->toBeString()
        ->toBe(dirname(__DIR__, 2) . $expected);
})->with([
    ['', '/packages/kickflip/source'],
    ['blue', '/packages/kickflip/source/blue'],
    ['hello/world', '/packages/kickflip/source/hello/world'],
]);

test('KickflipHelper::buildPath', function ($input, $expected) {
    expect(KickflipHelper::buildPath($input))
        ->toBeString()
        ->toBe(dirname(__DIR__, 2) . $expected);
})->with([
    ['', '/packages/kickflip/build_{env}'],
    ['blue', '/packages/kickflip/build_{env}/blue'],
    ['hello/world', '/packages/kickflip/build_{env}/hello/world'],
]);

test('KickflipHelper::buildPath macro replaceEnv', function ($buildPathInput, $envInput, $expected) {
    expect((string) Str::of(KickflipHelper::buildPath($buildPathInput))->replaceEnv($envInput))
        ->toBeString()
        ->toBe(dirname(__DIR__, 2) . $expected);
})->with([
    ['', 'prod', '/packages/kickflip/build_prod'],
    ['blue', 'site', '/packages/kickflip/build_site/blue'],
    ['hello/world', 'dev', '/packages/kickflip/build_dev/hello/world'],
]);
