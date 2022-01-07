<?php

declare(strict_types=1);

namespace KickflipMonoTests\Unit;

use Illuminate\Console\OutputStyle;
use Kickflip\Enums\ConsoleVerbosity;
use Kickflip\KickflipHelper;
use Kickflip\Logger;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\ReflectionHelpers;
use KickflipMonoTests\TestCase;
use Mockery;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;

class MockingLoggerTest extends TestCase
{
    use DataProviderHelpers;
    use ReflectionHelpers;

    /**
     * @var OutputStyle|LegacyMockInterface|MockInterface
     */
    public object $mockedConsoleOutput;

    /**
     * @var Logger|LegacyMockInterface|MockInterface
     */
    private object $loggerMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->mockedConsoleOutput = Mockery::mock(OutputStyle::class);
        Logger::setOutput($this->mockedConsoleOutput);
    }

    /**
     * @return array<string, array<array-key, string[]>>
     */
    public function basicStringDataProviders()
    {
        return self::autoAddDataProviderKeys([
            ['This is basic info logging text...'],
            ['This is basic more logging text...'],
            ['Hello there friends...'],
            ['Wat...'],
        ]);
    }

    /**
     * @dataProvider basicStringDataProviders
     */
    public function testInfoLogging(string $expectedAndInput): void
    {
        $this->mockedConsoleOutput->shouldReceive('writeln')
            ->once()->with($expectedAndInput);
        Logger::info($expectedAndInput);
        KickflipHelper::config()->set('output.verbosity', ConsoleVerbosity::quiet());
        $this->mockedConsoleOutput->shouldNotReceive('info');
        Logger::verbose($expectedAndInput);
    }

    /**
     * @dataProvider basicStringDataProviders
     */
    public function testVerboseLogging(string $expectedAndInput): void
    {
        KickflipHelper::config()->set('output.verbosity', ConsoleVerbosity::verbose());
        $this->mockedConsoleOutput->shouldReceive('info')
            ->once()->with($expectedAndInput);
        Logger::verbose($expectedAndInput);
        KickflipHelper::config()->set('output.verbosity', ConsoleVerbosity::quiet());
        $this->mockedConsoleOutput->shouldNotReceive('info');
        Logger::verbose($expectedAndInput);
    }

    /**
     * @dataProvider basicStringDataProviders
     */
    public function testVeryVerboseLogging(string $expectedAndInput): void
    {
        KickflipHelper::config()->set('output.verbosity', ConsoleVerbosity::veryVerbose());
        $this->mockedConsoleOutput->shouldReceive('info')
            ->once()->with($expectedAndInput);
        Logger::veryVerbose($expectedAndInput);
        KickflipHelper::config()->set('output.verbosity', ConsoleVerbosity::quiet());
        $this->mockedConsoleOutput->shouldNotReceive('info');
        Logger::veryVerbose($expectedAndInput);
    }

    /**
     * @dataProvider basicStringDataProviders
     */
    public function testDebugLogging(string $expectedAndInput): void
    {
        KickflipHelper::config()->set('output.verbosity', ConsoleVerbosity::debug());
        $this->mockedConsoleOutput->shouldReceive('warning')
            ->once()->with($expectedAndInput);
        Logger::debug($expectedAndInput);
        KickflipHelper::config()->set('output.verbosity', ConsoleVerbosity::quiet());
        $this->mockedConsoleOutput->shouldNotReceive('warning');
        Logger::debug($expectedAndInput);
    }

    /**
     * @param string[] $expectedAndInputHeaders
     * @param string[][] $expectedAndInputRows
     *
     * @dataProvider veryVerboseTableLoggingDataProviders
     */
    public function testVeryVerboseTableLogging(
        array $expectedAndInputHeaders,
        array $expectedAndInputRows,
    ): void {
        KickflipHelper::config()->set('output.verbosity', ConsoleVerbosity::veryVerbose());
        $this->mockedConsoleOutput->shouldReceive('table')
            ->once()->with($expectedAndInputHeaders, $expectedAndInputRows);
        Logger::veryVerboseTable($expectedAndInputHeaders, $expectedAndInputRows);
        KickflipHelper::config()->set('output.verbosity', ConsoleVerbosity::quiet());
        $this->mockedConsoleOutput->shouldNotReceive('table');
        Logger::veryVerboseTable($expectedAndInputHeaders, $expectedAndInputRows);
    }

    /**
     * @return array<array-key, string[]|string[][]>
     */
    public function veryVerboseTableLoggingDataProviders(): array
    {
        return self::autoAddDataProviderKeys([
            [
                ['Name', 'Route', 'Action'],
                [
                    ['Home Page', '/home', 'HomeAction::class'],
                    ['404 Page', '/404', 'FourOhFourPage::class'],
                ],
            ],
            [
                ['Colors'],
                [
                    ['red'],
                    ['blue'],
                    ['green'],
                ],
            ],
        ]);
    }
}
