<?php

declare(strict_types=1);

namespace KickflipMonoTests\Plugins\RouterNav;

use Kickflip\Models\PageData;
use Kickflip\RouterNavPlugin\Models\NavItem;
use Kickflip\SiteBuilder\SiteBuilder;
use KickflipMonoTests\DocsSite\DocsTestCase;

use function explode;
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

        // Init SiteBuilder to boot needed services...
        new SiteBuilder();
    }

    /**
     * @dataProvider navItemRawDataProvider
     */
    public function testBasicNavItemCanBeCreated(string $pageName, string $title, string $url, bool $hasRoute): void
    {
        [$type, $pageName] = explode(':', $pageName);
        if ($type === 'test') {
            $page = $this->getTestPageData((int) $pageName);
        } else {
            $page = $this->getDocsPageData($pageName);
        }
        $navItem = NavItem::makeFromRouteName($page->title, $page->source->getRouteName());
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
        self::assertEquals($page->source->getRouteName(), $navItem->getRouteName());
        self::assertEquals($page->source->getRouteName(), $navItem->routeName);
        // verify children
        self::assertFalse($navItem->hasChildren());
    }

    /**
     * @return array<array-key, PageData>
     */
    public function navItemRawDataProvider(): array
    {
        return $this->autoAddDataProviderKeys([
            ['docs:index', 'Getting Started', 'http://kickflip-docs.test', true],
            ['test:0', 'Basic', 'http://kickflip-docs.test/basic', true],
            ['test:6', 'Simple', 'http://kickflip-docs.test/simple', true],
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
        $basicPage = $this->getTestPageData();
        $indexNavItem = NavItem::make($indexPage->title, route('index'));
        $indexNavItem->setChildren([
            NavItem::make($basicPage->title, '/basic')
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
