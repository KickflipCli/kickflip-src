<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\Models;

use Kickflip\RouterNavPlugin\KickflipRouterNavServiceProvider;
use Kickflip\RouterNavPlugin\Models\NavItem;
use Kickflip\SiteBuilder\SiteBuilder;
use Kickflip\SiteBuilder\SourcesLocator;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\TestCase;

use function app;

class NavItemTest extends TestCase
{
    use DataProviderHelpers;

    public const ENV = 'dev';

    public function setUp(): void
    {
        parent::setUp();
        $this->prepareEnv();
    }

    protected function prepareEnv()
    {
        $this->app->register(KickflipRouterNavServiceProvider::class, true);
        SiteBuilder::includeEnvironmentConfig(static::ENV);
        SiteBuilder::updateBuildPaths(static::ENV);
        SiteBuilder::updateAppUrl();
        app(SourcesLocator::class);
    }

    /**
     * @dataProvider navItemRawDataProvider
     */
    public function testBasicNavItemCanBeCreated(string $title, string $url, bool $hasRoute, ?string $routeName): void
    {
        $navItem = NavItem::make($title, $url);
        // Verify title
        self::assertIsString($navItem->getLabel());
        self::assertEquals($title, $navItem->getLabel());
        self::assertIsString($navItem->title);
        self::assertEquals($title, $navItem->title);
        // Verify URL
        self::assertTrue($navItem->hasUrl());
        self::assertIsString($navItem->getUrl());
        self::assertEquals($url, $navItem->getUrl());
        self::assertIsString($navItem->url);
        self::assertEquals($url, $navItem->url);
        // Verify route name
        self::assertEquals($hasRoute, $navItem->hasRouteName());
        self::assertEquals($routeName, $navItem->getRouteName());
        self::assertEquals($routeName, $navItem->routeName);
        // verify children
        self::assertFalse($navItem->hasChildren());
    }

    /**
     * @return array<array-key, string[]>
     */
    public function navItemRawDataProvider(): array
    {
        return $this->autoAddDataProviderKeys([
            ['Home', '/', true, 'index'],
            ['Basic Page', '/basic', false, null],
            ['Another Page', '/another-page', false, null],
        ]);
    }

    public function testAnAdvancedNavItemCanBeMade(): void
    {
        $basicPage = NavItem::make('Basic Page', '/basic');
        $basicPage->setChildren([
            NavItem::make('Another Page', '/another-page'),
        ]);

        self::assertTrue($basicPage->hasChildren());
    }
}
