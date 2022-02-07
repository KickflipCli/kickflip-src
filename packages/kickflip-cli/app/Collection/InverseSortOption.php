<?php

declare(strict_types=1);

namespace Kickflip\Collection;

/**
 * @method static self name()
 * @method static self relativeDirectoryPath()
 * @mixin SortOption
 */
final class InverseSortOption extends SortOption
{
    /**
     * @return string[]
     */
    protected static function values(): array
    {
        return [
            'name' => '-name',
            'relativeDirectoryPath' => '-relativeDirectoryPath',
        ];
    }
}
