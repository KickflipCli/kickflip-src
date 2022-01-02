<?php

declare(strict_types=1);

use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Kickflip\Enums\ConsoleVerbosity;
use Symfony\Component\Console\Input\ArgvInput;

test('we can mock a realistic ArgvInput', function ($argvInput, $expected) {
    expect($argvInput)->toBeInstanceOf(ArgvInput::class);
    $parsedArgvInput = Str::of((string) $argvInput);
    expect($parsedArgvInput)
        ->toBeInstanceOf(Stringable::class);
    expect($parsedArgvInput->findVerbosity())
        ->toBeInstanceOf(ConsoleVerbosity::class)
        ->toBe($expected);
})->with([
    [
        new ArgvInput([
            './bin/kickflip',
            'build',
            '-q',
        ]),
        ConsoleVerbosity::quiet(),
    ],
    [
        new ArgvInput([
            'kickflip',
            'build',
            '--quiet',
        ]),
        ConsoleVerbosity::quiet(),
    ],
    [
        new ArgvInput([
            './kickflip',
            'build',
        ]),
        ConsoleVerbosity::normal(),
    ],
    [
        new ArgvInput([
            'kickflip',
            'build',
            '-v',
        ]),
        ConsoleVerbosity::verbose(),
    ],
    [
        new ArgvInput([
            '../kickflip-cli/bin/kickflip',
            'build',
            '-vv',
        ]),
        ConsoleVerbosity::veryVerbose(),
    ],
    [
        new ArgvInput([
            '../kickflip-cli/bin/kickflip',
            'build',
            '-vvv',
        ]),
        ConsoleVerbosity::debug(),
    ],
]);

test('ConsoleVerbosity enum values', function () {
    $reflectionClass = new ReflectionClass(ConsoleVerbosity::class);
    $valuesMethod = $reflectionClass->getMethod('values');
    $valuesMethod->setAccessible(true);
    expect($valuesMethod->invoke(null))
        ->toBeArray();
});
