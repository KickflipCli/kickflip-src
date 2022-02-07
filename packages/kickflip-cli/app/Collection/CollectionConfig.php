<?php

declare(strict_types=1);

namespace Kickflip\Collection;

use Kickflip\KickflipHelper;
use Kickflip\Models\ExtendsInfo;

use function str_starts_with;

class CollectionConfig
{
    public string $basePath;

    /**
     * @param SortOption|array<SortOption> $sort
     */
    public function __construct(
        public string $name,
        public string $path,
        public string $url,
        public ExtendsInfo | null $extends,
        public SortOption | array $sort
    ) {
        $this->basePath = KickflipHelper::sourcePath($this->path);
    }

    /**
     * @param SortOption|array<SortOption>|null $sort
     */
    public static function make(
        string $name,
        string | null $url = null,
        ExtendsInfo | null $extends = null,
        string | null $path = null,
        SortOption | array | null $sort = null,
    ): self {
        if ($url === null) {
            $url = $name;
        }
        if ($path === null) {
            $path = '_' . $name;
        } elseif (!str_starts_with($path, '_')) {
            $path = '_' . $path;
        }

        return new self(
            $name,
            $path,
            $url,
            $extends,
            $sort ?? [SortOption::name(), SortOption::relativeDirectoryPath()],
        );
    }
}
