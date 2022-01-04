<?php

declare(strict_types=1);

namespace Kickflip\RouterNavPlugin\Models;

use function count;

class NavItem implements NavItemInterface
{
    /**
     * @param array<array-key, NavItem>|null $children
     */
    public function __construct(
        public string $title,
        public string $url = '',
        public ?array $children = null,
    ) {
    }

    public static function make(string $title, ?string $url = ''): self
    {
        return new self($title, $url);
    }

    /**
     * @param array<self> $children
     *
     * @return $this
     */
    public function setChildren(array $children): self
    {
        $this->children = $children;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->title;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function hasUrl(): bool
    {
        return $this->url !== '';
    }

    public function hasChildren(): bool
    {
        return $this->children !== null && count($this->children) > 0;
    }
}
