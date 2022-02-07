<?php

declare(strict_types=1);

namespace Kickflip\Models;

use Illuminate\Support\Str;
use JetBrains\PhpStorm\Pure;
use Kickflip\Collection\CollectionConfig;
use Kickflip\KickflipHelper;
use Symfony\Component\Finder\SplFileInfo;

use const DIRECTORY_SEPARATOR;

final class SourcePageMetaData
{
    private string $viewName;
    /**
     * @var string|null Only set when created via fromSplFileInfoForCollection
     */
    private string | null $routeName = null;
    private string $implicitExtension;

    private function __construct(
        private string $filename,
        private string $relativePath,
        private string $explicitExtension,
        private string $fullPath,
    ) {
        $this->implicitExtension = (string) Str::of($filename)->after('.')->lower();
    }

    public static function fromSplFileInfo(SplFileInfo $fileInfo): self
    {
        $newInstance = new self(
            filename: $fileInfo->getFilename(),
            relativePath: $fileInfo->getRelativePath(),
            explicitExtension: $fileInfo->getExtension(),
            fullPath: $fileInfo->getPathname(),
        );

        $baseViewName = $newInstance->relativePath === '' ?
            Str::of($newInstance->filename)->lower() :
            Str::of($newInstance->filename)->prepend($newInstance->relativePath . DIRECTORY_SEPARATOR)->lower();
        $newInstance->viewName = (string) $baseViewName->beforeLast('.' . $newInstance->implicitExtension)
            ->replace(DIRECTORY_SEPARATOR, '.')->lower();

        return $newInstance;
    }

    public static function fromSplFileInfoForCollection(
        CollectionConfig $config,
        SplFileInfo $fileInfo
    ): self {
        $newInstance = self::fromSplFileInfo($fileInfo);

        $baseViewName = Str::of($newInstance->viewName);
        // TODO: make this part more sophisticated....
        $newInstance->routeName = (string) $baseViewName->replace($config->path, $config->url);

        return $newInstance;
    }

    #[Pure]
    public function getName(): string
    {
        return $this->viewName;
    }

    #[Pure]
    public function getRouteName(): string
    {
        return $this->routeName ?? $this->viewName;
    }

    #[Pure]
    public function getFilename(): string
    {
        return $this->filename;
    }

    #[Pure]
    public function getFullPath(): string
    {
        return $this->fullPath;
    }

    public function getRelativePath(): string
    {
        return (string) Str::of($this->fullPath)
            ->remove(KickflipHelper::sourcePath())
            ->prepend('source');
    }

    #[Pure]
    public function getRelativeDirectoryPath(): string
    {
        return $this->relativePath;
    }

    #[Pure]
    public function getExtension(): string
    {
        return $this->implicitExtension;
    }

    #[Pure]
    public function getMimeExtension(): string
    {
        return $this->explicitExtension;
    }

    #[Pure]
    public function getType(): string
    {
        return match ($this->implicitExtension) {
            'blade.php' => 'blade',
            'md.blade.php', 'blade.md', 'blade.markdown' => 'markdown w/ blade',
            'md', 'markdown' => 'markdown',
            default => 'unknown'
        };
    }
}
