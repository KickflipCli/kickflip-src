<?php

use Kickflip\KickflipHelper;
use League\CommonMark\Extension\FrontMatter\FrontMatterParserInterface;

test('default KickflipHelper::basePath', function () {
    expect(KickflipHelper::basePath())
        ->toBeString()
        ->toBe(dirname(__DIR__) . '/mock-app');
});

test('custom KickflipHelper::basePath', function ($input, $expected) {
    expect(KickflipHelper::basePath($input))
        ->toBeString()
        ->toBe(dirname(__DIR__, 2) . $expected);
})->with([
    ['./', ''],
    [null, ''],
    [__DIR__ . '/../mock-app', '/tests/mock-app'],
    [__DIR__, '/tests/Unit'],
]);

test('helper to kebab', function ($input, $expected) {
    expect(KickflipHelper::toKebab($input))
        ->toBeString()
        ->toBe($expected);
})->with([
    ['Hello World', 'hello-world'],
    ['Hello Kickflip!', 'hello-kickflip!'],
    ['Hello World, from kickflip!', 'hello-world,-from-kickflip!'],
]);

test('KickflipHelper::path', function ($input, $expected) {
    KickflipHelper::basePath(__DIR__ . '/../mock-app');
    expect(KickflipHelper::path($input))
        ->toBeString()
        ->toBe(dirname(__DIR__, 1) . $expected);
})->with([
    ['', '/mock-app'],
    ['blue', '/mock-app/blue'],
    ['hello/world', '/mock-app/hello/world'],
]);

test('KickflipHelper::getFrontmatterParser', function () {
    expect(KickflipHelper::getFrontMatterParser())
        ->toBeInstanceOf(FrontMatterParserInterface::class);
});

test('KickflipHelper::leftTrimPath', function (string $input, string $expected) {
    expect(KickflipHelper::leftTrimPath($input))
        ->toBeString()
        ->toBe($expected);
})->with([
    ['hello', 'hello'],
    ['/hello', 'hello'],
    ['/hello/', 'hello/'],
    ['hello/', 'hello/'],
]);

test('KickflipHelper::rightTrimPath', function (string $input, string $expected) {
    expect(KickflipHelper::rightTrimPath($input))
        ->toBeString()
        ->toBe($expected);
})->with([
    ['hello', 'hello'],
    ['/hello', '/hello'],
    ['/hello/', '/hello'],
    ['hello/', 'hello'],
]);

test('KickflipHelper::trimPath', function (string $input, string $expected) {
    expect(KickflipHelper::trimPath($input))
        ->toBeString()
        ->toBe($expected);
})->with([
    ['hello', 'hello'],
    ['/hello', 'hello'],
    ['/hello/', 'hello'],
    ['hello/', 'hello'],
]);

test('KickflipHelper::relativeUrl', function (string $input, string $expected) {
    expect(KickflipHelper::relativeUrl($input))
        ->toBeString()
        ->toBe($expected);
})->with([
    ['http://google.com/half-life/blackmesa/', 'http://google.com/half-life/blackmesa/'],
    ['https://google.com/half-life/blackmesa/', 'https://google.com/half-life/blackmesa/'],
    ['/half-life/blackmesa/', 'half-life/blackmesa'],
    ['/hello-world/', 'hello-world'],
    ['/half-life/blackmesa.html', 'half-life/blackmesa.html'],
]);
