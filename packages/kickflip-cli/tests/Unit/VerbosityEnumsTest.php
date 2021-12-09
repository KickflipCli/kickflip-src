<?php

use Kickflip\Enums\ConsoleVerbosity;
use Kickflip\Enums\VerbosityFlag;

test('VerbosityFlag enum can be constructed', function () {
    expect(VerbosityFlag::quiet())
        ->isEnumValue(VerbosityFlag::class, 'quiet');
    expect(VerbosityFlag::normal())
        ->isEnumValue(VerbosityFlag::class, 'normal');
    expect(VerbosityFlag::debug())
        ->isEnumValue(VerbosityFlag::class, 'vvv');
});

test('ConsoleVerbosity enum can be constructed', function () {
    expect(ConsoleVerbosity::quiet())
        ->isEnumValue(ConsoleVerbosity::class, 16);
    expect(ConsoleVerbosity::normal())
        ->isEnumValue(ConsoleVerbosity::class, 32);
    expect(ConsoleVerbosity::debug())
        ->isEnumValue(ConsoleVerbosity::class, 256);
});

test('ConsoleVerbosity can be constructed from VerbosityFlag', function () {
    expect(ConsoleVerbosity::fromFlag(VerbosityFlag::quiet()))
        ->toBeInstanceOf(ConsoleVerbosity::class);
    expect(ConsoleVerbosity::fromFlag(VerbosityFlag::normal()))
        ->toBeInstanceOf(ConsoleVerbosity::class);
    expect(ConsoleVerbosity::fromFlag(VerbosityFlag::debug()))
        ->toBeInstanceOf(ConsoleVerbosity::class);
});

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
