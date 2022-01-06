<?php

declare(strict_types=1);

namespace Kickflip\Models;

interface PageInterface extends ContentItemInterface
{
    /**
     * @param array<string, mixed> $frontMatter
     */
    public static function make(SourcePageMetaData $metaData, array $frontMatter = []): PageInterface;

    public function getUrl(): string;

    public function getOutputPath(): string;

    public function getExtendsView(): string | null;

    public function getExtendsSection(): string | null;

    public function getTitleId(): string;

    /**
     * @return array<string, mixed>
     */
    public function getExtraData(): array;

    public function __get(string $name);
}
