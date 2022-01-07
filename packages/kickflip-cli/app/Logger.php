<?php

declare(strict_types=1);

namespace Kickflip;

use Illuminate\Config\Repository;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Kickflip\Enums\ConsoleVerbosity;

use function app;
use function microtime;

class Logger
{
    protected static OutputStyle $consoleOutput;

    public static function timing(string $methodName, ?string $static = null): void
    {
        /**
         * @var Repository $timingsRepo
         */
        $timingsRepo = app('kickflipTimings');
        $index = Str::of($methodName)->afterLast('\\')->replace('::', '.');
        if ($static !== null) {
            $index = $index->replaceFirst(
                '.',
                Str::of($static)->afterLast('\\')->prepend('.extended.')->append('.'),
            );
        }
        $timingsRepo->set((string) $index, microtime(true));
    }

    public static function setOutput(OutputStyle &$output)
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
