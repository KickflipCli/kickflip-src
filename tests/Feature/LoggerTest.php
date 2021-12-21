<?php

use Kickflip\Enums\ConsoleVerbosity;
use Kickflip\Logger;

it('Logger::timing works correctly', function () {
    $timingsRepo = app('kickflipTimings');
    expect($timingsRepo)
        ->toBeInstanceOf(\Illuminate\Config\Repository::class)
        ->and($timingsRepo->all())
        ->toHaveCount(4);
    Logger::timing('NotStatic::stepOne');
    expect($timingsRepo)
        ->and($timingsRepo->all())
        ->toHaveCount(5)
        ->and($timingsRepo->get('NotStatic'))
        ->toBeArray()
        ->toHaveCount(1);
    Logger::timing('NotStatic::stepTwo', 'Static');
    expect($timingsRepo)
        ->and($timingsRepo->all())
        ->toHaveCount(5)
        ->and($timingsRepo->get('NotStatic'))
        ->toBeArray()
        ->toHaveCount(2);
    expect($timingsRepo)
        ->and($timingsRepo->all())
        ->toHaveCount(5);
    Logger::timing('Static::stepThree');
    expect($timingsRepo)
        ->and($timingsRepo->all())
        ->toHaveCount(6)
        ->and($timingsRepo->get('Static'))
        ->toBeArray()
        ->toHaveCount(1);
});
