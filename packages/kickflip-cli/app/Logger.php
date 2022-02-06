<?php

declare(strict_types=1);

namespace Kickflip;

use Illuminate\Config\Repository;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Kickflip\Enums\ConsoleVerbosity;
use RuntimeException;

use function microtime;

class Logger
{
    protected static OutputStyle $consoleOutput;

    private static Repository | null $kickflipTimings = null;

    public static function bootKickflipTimings(Repository $state)
    {
        self::$kickflipTimings = $state;
    }

    public static function getKickflipTimings(): Repository
    {
        if (self::$kickflipTimings === null) {
            throw new RuntimeException('Cannot access Kickflip timings state before initialized.');
        }

        return self::$kickflipTimings;
    }

    public static function timing(string $methodName, ?string $static = null): void
    {
        $timingsRepo = self::getKickflipTimings();
        $index = Str::of($methodName)->afterLast('\\')->replace('::', '.');
        if ($static !== null) {
            $index = $index->replaceFirst(
                '.',
                Str::of($static)->afterLast('\\')->prepend('.extended.')->append('.'),
            );
        }
        $timingsRepo->set((string) $index, microtime(true));
    }

    // phpcs:ignore
    public static function setOutput(OutputStyle &$output): void
    {
        static::$consoleOutput = $output;
    }

    public static function debug(string $message): void
    {
        if (ConsoleVerbosity::debug() <= KickflipHelper::config('output.verbosity')) {
            Log::debug($message);
            if (isset(static::$consoleOutput)) {
                static::$consoleOutput->warning($message);
            }
        }
    }

    /**
     * @param string[] $headers
     * @param array<string[]> $rows
     */
    public static function veryVerboseTable(array $headers, array $rows): void
    {
        if (
            ConsoleVerbosity::veryVerbose() <= KickflipHelper::config('output.verbosity') &&
            isset(static::$consoleOutput)
        ) {
            static::$consoleOutput->table($headers, $rows);
        }
    }

    public static function veryVerbose(string $message): void
    {
        if (ConsoleVerbosity::veryVerbose() <= KickflipHelper::config('output.verbosity')) {
            Log::debug($message);
            if (isset(static::$consoleOutput)) {
                static::$consoleOutput->info($message);
            }
        }
    }

    public static function verbose(string $message): void
    {
        if (ConsoleVerbosity::verbose() <= KickflipHelper::config('output.verbosity')) {
            Log::info($message);
            if (isset(static::$consoleOutput)) {
                static::$consoleOutput->info($message);
            }
        }
    }

    public static function info(string $message): void
    {
        if (ConsoleVerbosity::normal() <= KickflipHelper::config('output.verbosity')) {
            Log::info($message);
            if (isset(static::$consoleOutput)) {
                static::$consoleOutput->writeln($message);
            }
        }
    }
}
