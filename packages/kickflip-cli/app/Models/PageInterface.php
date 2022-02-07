<?php

declare(strict_types=1);

namespace Kickflip\Models;

use Kickflip\Collection\PageCollection;

interface PageInterface extends ContentItemInterface
{
    /**
     * @param array<string, mixed> $frontMatter
     */
    public static function make(SourcePageMetaData $metaData, array $frontMatter = []): PageInterface;

    /**
     * @param array<string, mixed> $frontMatter
     */
    public static function makeFromCollection(
        PageCollection $itemCollection,
        int $collectionIndex,
        SourcePageMetaData $metaData,
        array $frontMatter = []
    ): PageInterface;

    public function isCollectionItem(): bool;

    public function getExtendsView(): string | null;

    public function getExtendsSection(): string | null;

    public function getTitleId(): string;

    /**
     * @return array<string, mixed>
     */
    public function getExtraData(): array;

    public function __get(string $name);
}
