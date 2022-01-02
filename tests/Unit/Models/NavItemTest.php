<?php

declare(strict_types=1);

namespace KickflipMonoTests\Unit\Models;

use Kickflip\RouterNavPlugin\Models\NavItem;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\ReflectionHelpers;
use PHPUnit\Framework\TestCase;

class NavItemTest extends TestCase {
    use DataProviderHelpers;
    use ReflectionHelpers;

    public function testVerifyClassExists()
    {
        self::assertClassExists(NavItem::class);
    }

    /**
     * @dataProvider navItemRawDataProvider
     */
    public function testBasicNavItemCanBeCreated(string $title, string $url)
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
        // verify children
        self::assertFalse($navItem->hasChildren());
    }

    public function navItemRawDataProvider()
    {
        return $this->autoAddDataProviderKeys([
            ['Basic Page', '/basic'],
            ['Another Page', '/another-page'],
        ]);
    }

    public function testAnAdvancedNavItemCanBeMade()
    {
        $basicPage = NavItem::make('Basic Page', '/basic');
        $basicPage->setChildren([
            NavItem::make('Another Page', '/another-page')
        ]);

        self::assertTrue($basicPage->hasChildren());
    }
}
