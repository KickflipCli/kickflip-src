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
