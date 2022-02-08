<?php

declare(strict_types=1);

namespace Kickflip\Collection;

use Spatie\Enum\Enum;

/**
 * @method static self name()
 * @method static self relativeDirectoryPath()
 * @mixin SortOption
 */
final class InverseSortOption extends Enum implements SortOptionContract
{
    /**
     * @return array<string, string>
     */
    protected static function values(): array
    {
        return [
            'name' => '-name',
            'relativeDirectoryPath' => '-relativeDirectoryPath',
        ];
    }

    public function toFilter(): callable
    {
        // TODO: Implement toFilter() method.
    }
}
