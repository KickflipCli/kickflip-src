<?php

declare(strict_types=1);

namespace RepoBuilder;

use function sleep;

use const PHP_EOL;

trait SleepBuddy
{
    public static function sleepFor(int | float $seconds, int $increment = 5): void
    {
        for ($i = $seconds; $i > 0; $i = $i - $increment) {
            echo "Sleeping for a sec... ({$i} left)" . PHP_EOL;
            sleep($increment);
        }
    }
}
