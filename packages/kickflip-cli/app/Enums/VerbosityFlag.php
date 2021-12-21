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
final class VerbosityFlag extends Enum
{
    /**
     * @return string[]
     */
    protected static function values(): array
    {
        return [
            'quiet' => 'quiet',
            'normal' => 'normal',
            'verbose' => 'v',
            'veryVerbose' => 'vv',
            'debug' => 'vvv',
        ];
    }
}
