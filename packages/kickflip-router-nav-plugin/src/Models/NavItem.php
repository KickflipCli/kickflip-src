<?php

declare(strict_types=1);

namespace Kickflip\RouterNavPlugin\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Pure;
use Kickflip\KickflipHelper;
use Kickflip\Models\PageData;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function app;
use function count;

class NavItem implements NavItemInterface
{
    /**
     * @param array<array-key, NavItem>|null $children
     */
    public function __construct(
        public string $title,
        public string $url = '',
        public ?string $routeName = null,
        public ?array $children = null,
    ) {
    }

    #[Pure]
    public static function make(string $title, ?string $url = ''): self
    {
        // Try to find the route name if the URL starts with our base URL...
        $routeName = null;
        if (
            Str::startsWith($url, [
                KickflipHelper::rightTrimPath(KickflipHelper::config('site.baseUrl')),
                '/',
            ])
        ) {
            $fauxRequest = Request::create($url);
            try {
                $routeName = app('router')
                    ->getRoutes()
                    ->match($fauxRequest)
                    ->getName();
            } catch (NotFoundHttpException) {
            }
        }

        return new self(
            title: $title,
            url: $url,
            routeName: $routeName,
        );
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

    public function hasUrl(): bool
    {
        return $this->url !== '';
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function hasRouteName(): bool
    {
        return $this->routeName !== null;
    }

    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    public function hasChildren(): bool
    {
        return $this->children !== null && count($this->children) > 0;
    }

    #[Pure]
    public function matchesPage(PageData $page): bool
    {
        if ($this->hasRouteName()) {
            return $this->getRouteName() === KickflipHelper::pageRouteName($page);
        }

        return false;
    }
}
