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
    ['baseDir', '/tests/mock-app'],
    ['cache', '/tests/mock-app/cache'],
    ['env_config', '/tests/mock-app/config/config.{env}.php'],
]);

test('KickflipHelper::assetUrl', function ($input, $expected) {
    expect(KickflipHelper::assetUrl($input))
        ->isHtmlStringOf($expected);
})->with([
    ['', 'http://example.com/assets/'],
    ['blue', 'http://example.com/assets/blue'],
    ['hello/world', 'http://example.com/assets/hello/world'],
]);

test('KickflipHelper::resourcePath', function ($input, $expected) {
    expect(KickflipHelper::resourcePath($input))
        ->toBeString()
        ->toBe(dirname(__DIR__, 2) . $expected);
})->with([
    ['', '/tests/mock-app/resources'],
    ['blue', '/tests/mock-app/resources/blue'],
    ['hello/world', '/tests/mock-app/resources/hello/world'],
]);

test('KickflipHelper::sourcePath', function ($input, $expected) {
    expect(KickflipHelper::sourcePath($input))
        ->toBeString()
        ->toBe(dirname(__DIR__, 2) . $expected);
})->with([
    ['', '/tests/mock-app/source'],
    ['blue', '/tests/mock-app/source/blue'],
    ['hello/world', '/tests/mock-app/source/hello/world'],
]);

test('KickflipHelper::buildPath', function ($input, $expected) {
    expect(KickflipHelper::buildPath($input))
        ->toBeString()
        ->toBe(dirname(__DIR__, 2) . $expected);
})->with([
    ['', '/tests/mock-app/build_{env}'],
    ['blue', '/tests/mock-app/build_{env}/blue'],
    ['hello/world', '/tests/mock-app/build_{env}/hello/world'],
]);

test('KickflipHelper::buildPath macro replaceEnv', function ($buildPathInput, $envInput, $expected) {
    expect((string) Str::of(KickflipHelper::buildPath($buildPathInput))->replaceEnv($envInput))
        ->toBeString()
        ->toBe(dirname(__DIR__, 2) . $expected);
})->with([
    ['', 'prod', '/tests/mock-app/build_prod'],
    ['blue', 'site', '/tests/mock-app/build_site/blue'],
    ['hello/world', 'dev', '/tests/mock-app/build_dev/hello/world'],
]);
