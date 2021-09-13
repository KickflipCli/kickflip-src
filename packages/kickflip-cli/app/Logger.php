<?php

namespace Kickflip;

use Kickflip\Enums\ConsoleVerbosity;
use Illuminate\Config\Repository;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Support\Facades\Log;

class Logger
{
    public static function timing(string $methodName, ?string $static = null)
    {
        /**
         * @var Repository $timingsRepo
         */
        $timingsRepo = app('kickflipTimings');
        $index = Str::of($methodName)->afterLast('\\')->replace('::', '.');
        if (null !== $static) {
            $index = $index->replaceFirst(
                '.',
                Str::of($static)->afterLast('\\')->prepend('.extended.')->append('.')
            );
        }
        $timingsRepo->set((string) $index, microtime(true));
    }

    public static function debug(string $message)
    {
        if (ConsoleVerbosity::debug() <= app('kickflipCli')->get('output.verbosity')) {
            Log::debug($message);
        }
    }

    public static function veryVerbose(string $message)
    {
        if (ConsoleVerbosity::veryVerbose() <= app('kickflipCli')->get('output.verbosity')) {
            Log::debug($message);
        }
    }

    public static function verbose(string $message)
    {
        if (ConsoleVerbosity::verbose() <= app('kickflipCli')->get('output.verbosity')) {
            Log::info($message);
        }
    }

    public static function info(string $message)
    {
        if (ConsoleVerbosity::normal() <= app('kickflipCli')->get('output.verbosity')) {
            Log::info($message);
        }
    }
}
