<?php

use Kickflip\Enums\ConsoleVerbosity;
use Kickflip\Logger;

it('Logger::timing works correctly', function () {
    $timingsRepo = app('kickflipTimings');
    expect($timingsRepo)
        ->toBeInstanceOf(\Illuminate\Config\Repository::class)
        ->and($timingsRepo->all())
        ->toHaveCount(5);
    Logger::timing('NotStatic::stepOne');
    expect($timingsRepo)
        ->and($timingsRepo->all())
        ->toHaveCount(6)
        ->and($timingsRepo->get('NotStatic'))
        ->toBeArray()
        ->toHaveCount(1);
    Logger::timing('NotStatic::stepTwo', 'Static');
    expect($timingsRepo)
        ->and($timingsRepo->all())
        ->toHaveCount(6)
        ->and($timingsRepo->get('NotStatic'))
        ->toBeArray()
        ->toHaveCount(2);
    expect($timingsRepo)
        ->and($timingsRepo->all())
        ->toHaveCount(6);
    Logger::timing('Static::stepThree');
    expect($timingsRepo)
        ->and($timingsRepo->all())
        ->toHaveCount(7)
        ->and($timingsRepo->get('Static'))
        ->toBeArray()
        ->toHaveCount(1);
});

it('can try logging with a booted app', function () {
    Logger::info('This is a test');

    dd(
        app('log'),
    );
})->skip();
