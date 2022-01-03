<?php

declare(strict_types=1);

namespace Kickflip\Models;

interface PageInterface extends ContentItemInterface
{
    /**
     * @param array<string, mixed>  $frontMatter
     */
    public static function make(SourcePageMetaData $metaData, array $frontMatter = []): PageInterface;
    public function getUrl(): string;
    public function getOutputPath(): string;
    public function getExtendsView(): null|string;
    public function getExtendsSection(): null|string;
    public function getTitleId(): string;
    public function __get(string $name);
}
