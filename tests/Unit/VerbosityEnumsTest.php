<?php

use Kickflip\Enums\ConsoleVerbosity;
use Kickflip\Enums\VerbosityFlag;

test('VerbosityFlag enum can be constructed', function ($input, $expected) {
    expect($input)
        ->isEnumValue(VerbosityFlag::class, $expected);
})->with([
    [VerbosityFlag::quiet(), 'quiet'],
    [VerbosityFlag::normal(), 'normal'],
    [VerbosityFlag::verbose(), 'v'],
    [VerbosityFlag::veryVerbose(), 'vv'],
    [VerbosityFlag::debug(), 'vvv'],
]);

test('VerbosityFlag enum values', function () {
    $reflectionClass = new ReflectionClass(VerbosityFlag::class);
    $valuesMethod = $reflectionClass->getMethod('values');
    $valuesMethod->setAccessible(true);
    expect($valuesMethod->invoke(null))
        ->toBeArray();
});

test('ConsoleVerbosity enum can be constructed', function ($input, $expected) {
    expect($input)
        ->isEnumValue(ConsoleVerbosity::class, $expected);
})->with([
    [ConsoleVerbosity::quiet(), 16],
    [ConsoleVerbosity::normal(), 32],
    [ConsoleVerbosity::verbose(), 64],
    [ConsoleVerbosity::veryVerbose(), 128],
    [ConsoleVerbosity::debug(), 256],
]);

test('ConsoleVerbosity enum values', function () {
    $reflectionClass = new ReflectionClass(ConsoleVerbosity::class);
    $valuesMethod = $reflectionClass->getMethod('values');
    $valuesMethod->setAccessible(true);
    expect($valuesMethod->invoke(null))
        ->toBeArray();
});

test('ConsoleVerbosity can be constructed from VerbosityFlag', function ($input, $expected) {
    expect(ConsoleVerbosity::fromFlag($input))
        ->toBeInstanceOf(ConsoleVerbosity::class)
        ->toEqual($expected);
})->with([
    [VerbosityFlag::quiet(), ConsoleVerbosity::quiet()],
    [VerbosityFlag::normal(), ConsoleVerbosity::normal()],
    [VerbosityFlag::debug(), ConsoleVerbosity::debug()],
]);

test('ConsoleVerbosity values and labels', function () {
    expect(ConsoleVerbosity::toValues())
        ->toBeArray()
        ->toHaveLength(5)
        ->toContain(16)
        ->toContain(32)
        ->toContain(64)
        ->toContain(128)
        ->toContain(256);
    expect(ConsoleVerbosity::toLabels())
        ->toBeArray()
        ->toHaveLength(5)
        ->toContain('quiet')
        ->toContain('normal')
        ->toContain('verbose')
        ->toContain('veryVerbose')
        ->toContain('debug');
});

test('VerbosityFlag values and labels', function () {
    expect(VerbosityFlag::toValues())
        ->toBeArray()
        ->toHaveLength(5)
        ->toContain('quiet')
        ->toContain('normal')
        ->toContain('v')
        ->toContain('vv')
        ->toContain('vvv');
    expect(VerbosityFlag::toLabels())
        ->toBeArray()
        ->toHaveLength(5)
        ->toContain('quiet')
        ->toContain('normal')
        ->toContain('verbose')
        ->toContain('veryVerbose')
        ->toContain('debug');
});
