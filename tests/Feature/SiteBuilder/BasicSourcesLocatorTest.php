<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\SiteBuilder;

use Kickflip\Models\PageData;
use Kickflip\SiteBuilder\SiteBuilder;
use Kickflip\SiteBuilder\SourcesLocator;
use KickflipMonoTests\Feature\BaseFeatureTestCase;
use KickflipMonoTests\ReflectionHelpers;

use function dirname;

class BasicSourcesLocatorTest extends BaseFeatureTestCase
{
    use ReflectionHelpers;

    public function testItCanInstantiateSiteBuilder()
    {
        self::assertClassExists(SiteBuilder::class);
        $siteBuilder = new SiteBuilder();
        self::assertInstanceOf(SiteBuilder::class, $siteBuilder);
        self::assertHasProperty($siteBuilder, 'sourcesLocator');
    }

    public function testCanVerifySourcesLocatorProperties()
    {
        self::assertHasProperties(
            new SourcesLocator(dirname(__DIR__, 2) . '/sources'),
            [
                'sourcesBasePath',
                'renderPageList',
                'bladeSources',
                'markdownSources',
                'markdownBladeSources',
            ],
        );
    }

    public function testCanVerifySourcesLocatorMethods()
    {
        $sourceLocator = new SourcesLocator(dirname(__DIR__, 2) . '/sources');
        self::assertIsArray($sourceLocator->getRenderPageList());
        self::assertCount(9, $sourceLocator->getRenderPageList());
        foreach ($sourceLocator->getRenderPageList() as $pageData) {
            self::assertInstanceOf(PageData::class, $pageData);
        }
        self::assertIsArray($sourceLocator->getCopyFileList());
    }
}
