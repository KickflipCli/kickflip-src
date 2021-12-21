<?php

declare(strict_types=1);

namespace Kickflip\Models;

use Illuminate\Support\Str;
use Kickflip\KickflipHelper;

/**
 * @property string $sourceFile The path to the source file for this page.
 */
class ContentFileData implements ContentItemInterface
{
    private function __construct(
        public SourcePageMetaData $source,
        public string $url,
    ) {
    }

    /**
     * @param SourcePageMetaData $metaData
     * @param array<string, mixed> $frontMatter
     *
     * @return self
     */
    public static function make(SourcePageMetaData $metaData): self
    {
        $fileUrl = (string) Str::of($metaData->getFullPath())
                ->after(KickflipHelper::sourcePath());
        return new self(
            source: $metaData,
            url: $fileUrl,
        );
    }

    public function getUrl(): string
    {
        return KickflipHelper::relativeUrl($this->url);
    }

    public function getOutputPath(): string
    {
        return KickflipHelper::buildPath($this->getUrl());
    }
}
