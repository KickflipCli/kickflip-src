<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\Collections;

use Kickflip\Collection\PageCollection;
use Kickflip\KickflipHelper;
use Kickflip\Models\PageData;
use Kickflip\RouterNavPlugin\KickflipRouterNavServiceProvider;
use Kickflip\SiteBuilder\SiteBuilder;
use Kickflip\View\KickflipPaginator;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\Feature\BaseFeatureTestCase;
use KickflipMonoTests\ReflectionHelpers;

use function count;

class PageDataCollectionTest extends BaseFeatureTestCase
{
    use DataProviderHelpers;
    use ReflectionHelpers;

    public const ENV = 'dev';

    /**
     * This is necessary to allow NavItems to look up route names.
     */
    protected function prepareEnv(): void
    {
        $this->app->register(KickflipRouterNavServiceProvider::class);
        SiteBuilder::includeEnvironmentConfig(static::ENV);
        SiteBuilder::updateBuildPaths(static::ENV);
        SiteBuilder::updateAppUrl();

        // Init SiteBuilder to boot needed services...
        new SiteBuilder();
    }

    public function testCollectionsExist()
    {
        $this->prepareEnv();
        $collections = KickflipHelper::config('collections');
        self::assertIsArray($collections);
        self::assertEquals(2, count($collections));
        self::assertArrayHasKey('zombies', $collections);
        self::assertArrayHasKey('posts', $collections);
    }

    public function testCollectionItemMethods()
    {
        $this->prepareEnv();
        $collections = KickflipHelper::config('collections');
        self::assertInstanceOf(PageCollection::class, $collections['zombies']);
        $pageData = $collections['zombies']->getItems()[0];
        self::assertInstanceOf(PageData::class, $pageData);
        self::assertTrue($pageData->isCollectionItem());
        self::assertIsString($pageData->getCollectionName());
        self::assertIsInt($pageData->getCollectionIndex());
        self::assertInstanceOf(PageCollection::class, $pageData->getCollection());
        KickflipHelper::config()->set('page', $pageData);
        self::assertInstanceOf(KickflipPaginator::class, $pageData->getPreviousNextPaginator());
        self::assertInstanceOf(KickflipPaginator::class, $pageData->getPaginator());
    }
}
