<?php

namespace Kickflip\Models;

interface NavItemInterface
{
    public static function make(string $title, ?string $url = null): NavItemInterface;

    /**
     * @param array<NavItemInterface> $children
     * @return NavItemInterface
     */
    public function setChildren(array $children): NavItemInterface;

    public function getLabel(): string;

    public function getUrl(): string;

    public function hasUrl(): bool;

    public function hasChildren(): bool;
}
