<?php

declare(strict_types=1);

namespace Kickflip\RouterNavPlugin\Models;

interface NavItemInterface
{
    public static function make(string $title, ?string $url = ''): NavItemInterface;

    /**
     * @param array<array-key, NavItem> $children
     */
    public function setChildren(array $children): NavItemInterface;

    public function getLabel(): string;

    public function getUrl(): string;

    public function hasUrl(): bool;

    public function hasChildren(): bool;
}
