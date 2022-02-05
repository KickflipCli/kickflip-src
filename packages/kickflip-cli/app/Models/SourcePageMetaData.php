<?php

declare(strict_types=1);

namespace Kickflip\Models;

use Illuminate\Support\Str;
use Kickflip\KickflipHelper;
use Symfony\Component\Finder\SplFileInfo;

use const DIRECTORY_SEPARATOR;

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
        $this->implicitExtension = (string) Str::of($filename)->after('.')->lower();
        $baseViewName = $relativePath === '' ?
            Str::of($filename)->lower() :
            Str::of($filename)->prepend($this->relativePath . DIRECTORY_SEPARATOR)->lower();
        $this->viewName = (string) $baseViewName->beforeLast('.' . $this->implicitExtension)
            ->replace(DIRECTORY_SEPARATOR, '.')->lower();
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

    public function getRelativePath(): string
    {
        return (string) Str::of($this->fullPath)
            ->remove(KickflipHelper::sourcePath())
            ->prepend('source');
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
