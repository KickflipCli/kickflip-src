<?php

declare(strict_types=1);

namespace KickflipMonoTests\Unit\Enums;

use Kickflip\Enums\ConsoleVerbosity;
use Kickflip\Enums\VerbosityFlag;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\ReflectionHelpers;
use PHPUnit\Framework\TestCase;

class VerbosityEnumsTest extends TestCase
{
    use DataProviderHelpers;
    use ReflectionHelpers;

    /**
     * @dataProvider verbosityFlagProvider
     */
    public function testItCanConstructVerbosityFlag(VerbosityFlag $input, string $expected)
    {
        self::assertInstanceOf(VerbosityFlag::class, $input);
        self::assertIsScalar($input->value);
        self::assertIsString($input->value);
        self::assertEquals($expected, $input->value);
    }

    public function verbosityFlagProvider(): array
    {
        return $this->autoAddDataProviderKeys([
            [VerbosityFlag::quiet(), 'quiet'],
            [VerbosityFlag::normal(), 'normal'],
            [VerbosityFlag::verbose(), 'v'],
            [VerbosityFlag::veryVerbose(), 'vv'],
            [VerbosityFlag::debug(), 'vvv'],
        ]);
    }

    public function testItCanVerifyEnumValues()
    {
        self::assertIsArray(self::reflectionCallMethod(VerbosityFlag::class, 'values'));
    }

    /**
     * @dataProvider consoleVerbosityProvider
     */
    public function testItCanConstructConsoleVerbosity(ConsoleVerbosity $input, int $expected)
    {
        self::assertInstanceOf(ConsoleVerbosity::class, $input);
        self::assertIsScalar($input->value);
        self::assertIsInt($input->value);
        self::assertEquals($expected, $input->value);
    }

    public function consoleVerbosityProvider(): array
    {
        return $this->autoAddDataProviderKeys([
            [ConsoleVerbosity::quiet(), 16],
            [ConsoleVerbosity::normal(), 32],
            [ConsoleVerbosity::verbose(), 64],
            [ConsoleVerbosity::veryVerbose(), 128],
            [ConsoleVerbosity::debug(), 256],
        ]);
    }

    public function testItCanVerifyConsoleVerbosityEnumValues()
    {
        self::assertIsArray(self::reflectionCallMethod(ConsoleVerbosity::class, 'values'));
    }

    /**
     * @dataProvider consoleVerbosityFromVerbosityFlagProvider
     */
    public function testItCanConstructConsoleVerbosityFromVerbosityFlag($input, $expected)
    {
        $consoleVerbosity = ConsoleVerbosity::fromFlag($input);
        self::assertInstanceOf(ConsoleVerbosity::class, $consoleVerbosity);
        self::assertEquals($expected, $consoleVerbosity);
    }

    public function consoleVerbosityFromVerbosityFlagProvider(): array
    {
        return $this->autoAddDataProviderKeys([
            [VerbosityFlag::quiet(), ConsoleVerbosity::quiet()],
            [VerbosityFlag::normal(), ConsoleVerbosity::normal()],
            [VerbosityFlag::debug(), ConsoleVerbosity::debug()],
        ]);
    }

    public function testConsoleVerbosityValues()
    {
        $values = ConsoleVerbosity::toValues();
        self::assertIsArray($values);
        self::assertCount(5, $values);
        self::assertContains(16, $values);
        self::assertContains(32, $values);
        self::assertContains(64, $values);
        self::assertContains(128, $values);
        self::assertContains(256, $values);
    }

    public function testConsoleVerbosityLabels()
    {
        $labels = ConsoleVerbosity::toLabels();
        self::assertIsArray($labels);
        self::assertCount(5, $labels);
        self::assertContains('quiet', $labels);
        self::assertContains('normal', $labels);
        self::assertContains('verbose', $labels);
        self::assertContains('veryVerbose', $labels);
        self::assertContains('debug', $labels);
    }

    public function testVerbosityFlagValues()
    {
        $values = VerbosityFlag::toValues();
        self::assertIsArray($values);
        self::assertCount(5, $values);
        self::assertContains('quiet', $values);
        self::assertContains('normal', $values);
        self::assertContains('v', $values);
        self::assertContains('vv', $values);
        self::assertContains('vvv', $values);
    }

    public function testVerbosityFlagLabels()
    {
        $labels = VerbosityFlag::toLabels();
        self::assertIsArray($labels);
        self::assertCount(5, $labels);
        self::assertContains('quiet', $labels);
        self::assertContains('normal', $labels);
        self::assertContains('verbose', $labels);
        self::assertContains('veryVerbose', $labels);
        self::assertContains('debug', $labels);
    }
}
