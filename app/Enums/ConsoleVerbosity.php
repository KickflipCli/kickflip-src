<?php

declare(strict_types=1);

namespace Kickflip\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self quiet()
 * @method static self normal()
 * @method static self verbose()
 * @method static self veryVerbose()
 * @method static self debug()
 */
class ConsoleVerbosity extends Enum
{
    public static function fromFlag(VerbosityFlag $flag): ConsoleVerbosity
    {
        return static::{$flag->label}();
    }

    /**
     * @return int[]
     */
    protected static function values(): array
    {
        return [
            'quiet' => 16,
            'normal' => 32,
            'verbose' => 64,
            'veryVerbose' => 128,
            'debug' => 256,
        ];
    }
}
