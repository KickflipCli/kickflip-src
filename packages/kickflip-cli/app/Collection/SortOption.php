<?php

declare(strict_types=1);

namespace Kickflip\Collection;

use Kickflip\Models\PageData;
use RuntimeException;
use Spatie\Enum\Enum;

use function count;
use function md5;
use function serialize;

/**
 * @method static self custom()
 * @method static self name()
 * @method static self relativeDirectoryPath()
 */
class SortOption extends Enum
{
    /** @psalm-var array<class-string, array<int|string, SortOption>> */
    private static array $instances = [];
    public mixed $backedValue;

    public static function __callStatic(string $name, array $arguments)
    {
        return static::fromBacked($name, $arguments);
    }

    /**
     * @param array<int|string|bool> $arguments
     *
     * @return static
     */
    final public static function fromBacked(string | int $value, array $arguments): SortOption
    {
        $backedValueHash = md5(serialize($arguments));
        if (!isset(self::$instances[static::class][$value][$backedValueHash])) {
            $enum = new SortOption($value, $arguments);
            self::$instances[static::class][$enum->value][$backedValueHash] = $enum;
        }

        return self::$instances[static::class][$value][$backedValueHash];
    }

    /**
     * @internal
     *
     * @param string|int $value
     */
    public function __construct($value, array | null $arguments = null)
    {
        if ($arguments !== null && count($arguments) > 0) {
            $this->backedValue = $arguments[0];
        }

        parent::__construct($value);
    }

    public function toFilter(): callable
    {
        return match ($this->value) {
            'name' => static fn (PageData $item) => $item->source->getName(),
            'relativePath' => static fn (PageData $item) => $item->source->getRelativePath(),
            'relativeDirectoryPath' => static fn (PageData $item) => $item->source->getRelativeDirectoryPath(),
            'custom' => fn (PageData $item) => $item->{$this->backedValue},
            default => throw new RuntimeException('Invalid enum state'),
        };
    }
}
