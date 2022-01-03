<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature;

use Illuminate\Config\Repository;
use Kickflip\Logger;
use KickflipMonoTests\TestCase;

use function app;

class LoggerTest extends TestCase
{
    public function testLoggerTimingWorksCorrectly()
    {
        $timingsRepo = app('kickflipTimings');
        self::assertInstanceOf(Repository::class, $timingsRepo);
        self::assertCount(4, $timingsRepo->all());

        // Make Step 1
        Logger::timing('NotStatic::stepOne');
        self::assertCount(5, $timingsRepo->all());
        self::assertIsArray($timingsRepo->get('NotStatic'));
        self::assertCount(1, $timingsRepo->get('NotStatic'));

        // Make step 2
        Logger::timing('NotStatic::stepTwo', 'Static');
        self::assertCount(5, $timingsRepo->all());
        self::assertIsArray($timingsRepo->get('NotStatic'));
        self::assertCount(2, $timingsRepo->get('NotStatic'));

        // Make step 3
        Logger::timing('Static::stepThree');
        self::assertCount(6, $timingsRepo->all());
        self::assertIsArray($timingsRepo->get('Static'));
        self::assertCount(1, $timingsRepo->get('Static'));
    }
}
