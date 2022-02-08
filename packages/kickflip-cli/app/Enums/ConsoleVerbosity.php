<?php

declare(strict_types=1);

namespace Kickflip\Enums;

use JetBrains\PhpStorm\ArrayShape;
use Spatie\Enum\Enum;

/**
 * @method static self quiet()
 * @method static self normal()
 * @method static self verbose()
 * @method static self veryVerbose()
 * @method static self debug()
 */
final class ConsoleVerbosity extends Enum
{
    public static function fromFlag(VerbosityFlag $flag): ConsoleVerbosity
    {
        return self::{$flag->label}();
    }

    /**
     * @return array<string, int>
     */
    #[ArrayShape([
        'quiet' => 'string',
        'normal' => 'string',
        'verbose' => 'string',
        'veryVerbose' => 'string',
        'debug' => 'string',
    ])]
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
