<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature;

use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Kickflip\Enums\ConsoleVerbosity;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\ReflectionHelpers;
use Symfony\Component\Console\Input\ArgvInput;

class ConsoleVerbosityBaseFeatureTest extends BaseFeatureTestCase
{
    use DataProviderHelpers;
    use ReflectionHelpers;

    public function testConsoleVerbosityEnumValues()
    {
        $consoleValues = self::reflectionCallMethod(ConsoleVerbosity::class, 'values');
        self::assertIsArray($consoleValues);
    }

    /**
     * @dataProvider mockedArgvProvider
     */
    public function testCanMockRealisticArgvInput(ArgvInput $argvInput, ConsoleVerbosity $expected)
    {
        self::assertInstanceOf(ArgvInput::class, $argvInput);
        $parsedArgvInput = Str::of((string) $argvInput);
        $parsedVerbosity = $parsedArgvInput->findVerbosity();
        self::assertInstanceOf(Stringable::class, $parsedArgvInput);
        self::assertInstanceOf(ConsoleVerbosity::class, $parsedVerbosity);
        self::assertEquals($expected, $parsedVerbosity);
    }

    /**
     * @return array<array-key, array<array-key, ArgvInput|ConsoleVerbosity>>
     */
    public function mockedArgvProvider(): array
    {
        return $this->autoAddDataProviderKeys([
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
    }
}
