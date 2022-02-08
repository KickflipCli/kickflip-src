<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\Collections;

use Kickflip\KickflipHelper;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\Feature\BaseFeatureTestCase;
use KickflipMonoTests\ReflectionHelpers;
use RuntimeException;

class PageDataCollectionExceptionTest extends BaseFeatureTestCase
{
    use DataProviderHelpers;
    use ReflectionHelpers;

    public function testCollectionsDontExist()
    {
        $collections = KickflipHelper::config('collections');
        self::assertNull($collections);
    }

    public function testExpectExceptionGetCollectionName()
    {
        $indexPage = $this->getTestPageData(0);
        self::assertFalse($indexPage->isCollectionItem());
        // Test some exceptions...
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Should only call `getCollectionName` on PageData part of a collection');
        $indexPage->getCollectionName();
    }

    public function testExpectExceptionGetCollectionIndex()
    {
        $indexPage = $this->getTestPageData(0);
        self::assertFalse($indexPage->isCollectionItem());
        // Test some exceptions...
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Should only call `getCollectionIndex` on PageData part of a collection');
        $indexPage->getCollectionIndex();
    }

    public function testExpectExceptionUpdateCollectionIndex()
    {
        $indexPage = $this->getTestPageData(0);
        self::assertFalse($indexPage->isCollectionItem());
        // Test some exceptions...
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Cannot set collection index on page not in a collection');
        $indexPage->updateCollectionIndex(42);
    }

    public function testExpectExceptionGetCollection()
    {
        $indexPage = $this->getTestPageData(0);
        self::assertFalse($indexPage->isCollectionItem());
        // Test some exceptions...
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Should only call `getCollection` on PageData part of a collection');
        $indexPage->getCollection();
    }

    public function testExpectExceptionGetPreviousNextPaginator()
    {
        $indexPage = $this->getTestPageData(0);
        self::assertFalse($indexPage->isCollectionItem());
        // Test some exceptions...
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Should only call `getPreviousNextPaginator` on PageData part of a collection');
        $indexPage->getPreviousNextPaginator();
    }

    public function testExpectNullGetPaginator()
    {
        $indexPage = $this->getTestPageData(0);
        self::assertFalse($indexPage->isCollectionItem());
        // Test some exceptions...
        self::assertNull($indexPage->getPaginator());
    }
}
