<?php

use Illuminate\Support\Str;
use Kickflip\KickflipHelper;

test('string macro replaceEnv', function ($input, $expected) {
    $stringFormat = Str::of("Hello, {env}");
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
    ['baseDir', '/packages/kickflip-docs'],
    ['cache', '/packages/kickflip-docs/cache'],
    ['env_config', '/packages/kickflip-docs/config/config.{env}.php'],
]);

test('KickflipHelper::assetUrl', function ($input, $expected) {
    expect(KickflipHelper::assetUrl($input))
        ->isHtmlStringOf($expected);
})->with([
    ['', 'http://kickflip-docs.test/assets/'],
    ['blue', 'http://kickflip-docs.test/assets/blue'],
    ['hello/world', 'http://kickflip-docs.test/assets/hello/world'],
]);

test('KickflipHelper::resourcePath', function ($input, $expected) {
    expect(KickflipHelper::resourcePath($input))
        ->toBeString()
        ->toBe(dirname(__DIR__, 2) . $expected);
})->with([
    ['', '/packages/kickflip-docs/resources'],
    ['blue', '/packages/kickflip-docs/resources/blue'],
    ['hello/world', '/packages/kickflip-docs/resources/hello/world'],
]);

test('KickflipHelper::sourcePath', function ($input, $expected) {
    expect(KickflipHelper::sourcePath($input))
        ->toBeString()
        ->toBe(dirname(__DIR__, 2) . $expected);
})->with([
    ['', '/packages/kickflip-docs/source'],
    ['blue', '/packages/kickflip-docs/source/blue'],
    ['hello/world', '/packages/kickflip-docs/source/hello/world'],
]);

test('KickflipHelper::buildPath', function ($input, $expected) {
    expect(KickflipHelper::buildPath($input))
        ->toBeString()
        ->toBe(dirname(__DIR__, 2) . $expected);
})->with([
    ['', '/packages/kickflip-docs/build_{env}'],
    ['blue', '/packages/kickflip-docs/build_{env}/blue'],
    ['hello/world', '/packages/kickflip-docs/build_{env}/hello/world'],
]);

test('KickflipHelper::buildPath macro replaceEnv', function ($buildPathInput, $envInput, $expected) {
    expect((string) Str::of(KickflipHelper::buildPath($buildPathInput))->replaceEnv($envInput))
        ->toBeString()
        ->toBe(dirname(__DIR__, 2) . $expected);
})->with([
    ['', 'prod', '/packages/kickflip-docs/build_prod'],
    ['blue', 'site', '/packages/kickflip-docs/build_site/blue'],
    ['hello/world', 'dev', '/packages/kickflip-docs/build_dev/hello/world'],
]);
