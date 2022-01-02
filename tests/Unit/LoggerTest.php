<?php

declare(strict_types=1);

use Kickflip\Logger;

test('Logger class exists', function () {
    $this->assertTrue(class_exists(Logger::class));
    expect(Logger::class)->toBeString()->reflectHasProperty('consoleOutput');
});

it('fails without access to global kickflipCli', function (string $logLevel) {
    Logger::{$logLevel}('test');
})->throws(Exception::class, 'Target class [kickflipCli] does not exist.')->with([
    'debug',
    'veryVerbose',
    'verbose',
    'info',
]);

it('fails veryVerboseTable without access to global kickflipCli', function () {
    Logger::veryVerboseTable([], []);
})->throws(Exception::class, 'Target class [kickflipCli] does not exist.');

it('fails timing without access to global kickflipTimings', function () {
    Logger::timing('yeetBoy', 'NotStatic');
})->throws(Exception::class, 'Target class [kickflipTimings] does not exist.');
