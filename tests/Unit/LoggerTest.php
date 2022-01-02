<?php

declare(strict_types=1);

namespace KickflipMonoTests\Unit;

use Kickflip\Logger;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\ReflectionHelpers;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase {
    use DataProviderHelpers, ReflectionHelpers;

    public function testCanVerifyClassExists(): void
    {
        self::assertClassExists(Logger::class);
        self::assertHasProperty(Logger::class, 'consoleOutput');
    }

    /**
     * @dataProvider logLevelDataProvider
     */
    public function testItFailsWithoutAccessToKickflip(string $logLevel)
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Target class [kickflipCli] does not exist.');
        Logger::{$logLevel}('test');
    }

    public function logLevelDataProvider()
    {
        return $this->autoAddDataProviderKeys([
            ['debug'],
            ['veryVerbose'],
            ['verbose'],
            ['info'],
        ]);
    }

    public function testTablesFailWithoutKickflip()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Target class [kickflipCli] does not exist.');
        Logger::veryVerboseTable([], []);
    }

    public function testtimingFailWithoutKickflipTimings()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Target class [kickflipTimings] does not exist.');
        Logger::timing('yeetBoy', 'NotStatic');
    }
}
