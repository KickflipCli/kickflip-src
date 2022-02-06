<?php

declare(strict_types=1);

namespace KickflipMonoTests\Plugins\RouterNav;

use Kickflip\RouterNavPlugin\Models\NavItem;
use Kickflip\SiteBuilder\SiteBuilder;
use Kickflip\SiteBuilder\SourcesLocator;
use KickflipMonoTests\DocsSite\DocsTestCase;
use function app;
use function route;

class DocsNavItemTest extends DocsTestCase
{
    public const ENV = 'dev';

    public function setUp(): void
    {
        parent::setUp();
        $this->prepareEnv();
    }

    /**
     * This is necessary to allow NavItems to look up route names.
     */
    protected function prepareEnv(): void
    {
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
        $index = NavItem::make('Home', route('index'));
        $index->setChildren([
            NavItem::make('Basic Page', '/basic')
                ->setChildren([
                    NavItem::make('Another Page', '/another-page'),
                ]),
        ]);

        self::assertTrue($index->hasChildren());
        self::assertTrue($index->children[0]->hasChildren());
        self::assertFalse($index->children[0]->children[0]->hasChildren());
    }

    public function testCanVerifyNavItemMatchPage()
    {
        $indexPage = $this->getDocsPageData('index');
        $indexNavItem = NavItem::make('Home', route('index'));
        $indexNavItem->setChildren([
            NavItem::make('Basic Page', '/basic')
                ->setChildren([
                    NavItem::make('Another Page', '/another-page'),
                ]),
        ]);

        // Check the matchesPage method now...
        self::assertTrue($indexNavItem->matchesPage($indexPage));
        self::assertFalse($indexNavItem->children[0]->matchesPage($indexPage));
        self::assertFalse($indexNavItem->children[0]->children[0]->matchesPage($indexPage));
    }
}
