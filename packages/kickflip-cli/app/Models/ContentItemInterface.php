<?php

declare(strict_types=1);

namespace Kickflip\Models;

interface ContentItemInterface
{
    public static function make(SourcePageMetaData $metaData): self;
    public function getUrl(): string;
    public function getOutputPath(): string;
}
