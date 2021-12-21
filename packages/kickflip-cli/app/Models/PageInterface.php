<?php

declare(strict_types=1);

namespace Kickflip\Models;

interface PageInterface
{
    public static function make(SourcePageMetaData $metaData, array $frontMatter = []): self;
    public function getUrl(): string;
    public function getOutputPath(): string;
    public function getExtendsView(): string;
    public function getExtendsSection(): string;
    public function getTitleId(): string;
    public function __get(string $name);
}
