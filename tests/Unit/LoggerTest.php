<?php

declare(strict_types=1);

namespace KickflipMonoTests\Unit;

use Kickflip\Logger;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\ReflectionHelpers;
use RuntimeException;

class LoggerTest extends BaseUnitTestCase
{
    use DataProviderHelpers;
    use ReflectionHelpers;

    public function testCanVerifyClassExists(): void
    {
        self::assertClassExists(Logger::class);
        self::assertHasProperty(Logger::class, 'consoleOutput');
    }

    /**
     * @dataProvider logLevelDataProvider
     */
    public function testItFailsWithoutAccessToKickflip(string $logLevel): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot access Kickflip state before initialized.');
        Logger::{$logLevel}('test');
    }

    /**
     * @return array<array-key, string[]>
     */
    public function logLevelDataProvider(): array
    {
        return $this->autoAddDataProviderKeys([
            ['debug'],
            ['veryVerbose'],
            ['verbose'],
            ['info'],
        ]);
    }

    public function testTablesFailWithoutKickflip(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot access Kickflip state before initialized.');
        Logger::veryVerboseTable([], []);
    }

    public function testTimingFailWithoutKickflipTimings(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot access Kickflip timings state before initialized.');
        Logger::timing('yeetBoy', 'NotStatic');
    }
}
