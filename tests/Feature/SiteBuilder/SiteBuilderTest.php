<?php

namespace KickflipMonoTests\Feature\SiteBuilder;

use Kickflip\KickflipHelper;
use Kickflip\Models\PageData;
use Kickflip\SiteBuilder\ShikiNpmFetcher;
use Kickflip\SiteBuilder\SiteBuilder;
use Kickflip\SiteBuilder\SourcesLocator;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\TestCase;

class SiteBuilderTest extends TestCase
{
    use DataProviderHelpers;
    protected const ENV = 'local';

    public function setUp(): void
    {
        parent::setUp();
        SiteBuilder::includeEnvironmentConfig(static::ENV);
        SiteBuilder::updateBuildPaths(static::ENV);
        SiteBuilder::updateAppUrl();
        $shikiNpmFetcher = app(ShikiNpmFetcher::class);
        if (!$shikiNpmFetcher->isShikiDownloaded()) {
            $shikiNpmFetcher->installShiki();
        }
    }

    public function tearDown(): void
    {
        $shikiNpmFetcher = app(ShikiNpmFetcher::class);
        if ($shikiNpmFetcher->isShikiDownloaded()) {
            $shikiNpmFetcher->removeShikiAndNodeModules();
        }
        parent::tearDown();
    }

    /**
     * @dataProvider renderListDataProvider
     */
    public function testItWillProduceExpectedHtml(PageData $page): void
    {
        $view = view($page->source->getName(), [
            'page' => $page,
        ]);
        self::assertMatchesHtmlSnapshot($view->render());
    }

    public function renderListDataProvider()
    {
        $this->refreshApplication();
        $sourceLocator = app(SourcesLocator::class);
        return self::autoAddDataProviderKeys($sourceLocator->getRenderPageList());
    }
}
