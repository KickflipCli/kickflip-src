<?php

declare(strict_types=1);

namespace Kickflip\Models;

use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

final class SourcePageMetaData
{
    private string $viewName;
    private string $implicitExtension;

    private function __construct(
        private string $filename,
        private string $relativePath,
        private string $explicitExtension,
        private string $fullPath,
    ) {
        $this->implicitExtension = (string) Str::of($filename)->after('.');
        $baseViewName = ('' === $relativePath) ? Str::of($filename) : Str::of($filename)->prepend($this->relativePath . DIRECTORY_SEPARATOR);
        $this->viewName = (string) $baseViewName->beforeLast('.' . $this->implicitExtension)->replace(DIRECTORY_SEPARATOR, '.');
    }

    public static function fromSplFileInfo(SplFileInfo $fileInfo): self
    {
        return new self(
            filename: $fileInfo->getFilename(),
            relativePath: $fileInfo->getRelativePath(),
            explicitExtension: $fileInfo->getExtension(),
            fullPath: $fileInfo->getPathname(),
        );
    }

    public function getName(): string
    {
        return $this->viewName;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getFullPath(): string
    {
        return $this->fullPath;
    }

    public function getExtension(): string
    {
        return $this->implicitExtension;
    }

    public function getMimeExtension(): string
    {
        return $this->explicitExtension;
    }

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
