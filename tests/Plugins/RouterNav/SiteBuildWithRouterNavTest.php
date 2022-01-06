<?php

declare(strict_types=1);

namespace KickflipMonoTests\Plugins\RouterNav;

use Kickflip\Models\PageData;
use Kickflip\RouterNavPlugin\KickflipRouterNavServiceProvider;
use Kickflip\SiteBuilder\ShikiNpmFetcher;
use Kickflip\SiteBuilder\SiteBuilder;
use Kickflip\SiteBuilder\SourcesLocator;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\TestCase;

use function app;
use function view;

class SiteBuildWithRouterNavTest extends TestCase
{
    use DataProviderHelpers;

    protected const ENV = 'production';

    public function setUp(): void
    {
        parent::setUp();
        $this->prepareProdEnv();
        $shikiNpmFetcher = app(ShikiNpmFetcher::class);
        if (!$shikiNpmFetcher->isShikiDownloaded()) {
            $shikiNpmFetcher->installShiki();
        }
    }

    protected function prepareProdEnv()
    {
        $this->app->register(KickflipRouterNavServiceProvider::class);
        SiteBuilder::includeEnvironmentConfig(static::ENV);
        SiteBuilder::updateBuildPaths(static::ENV);
        SiteBuilder::updateAppUrl();
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

        self::assertMatchesHtmlSnapshot(self::stripMixIdsFromHtml($view->render()));
    }

    /**
     * @return array<string, array{PageData}>
     */
    public function renderListDataProvider(): array
    {
        $this->refreshApplication();
        $this->prepareProdEnv();
        $this->app->register(KickflipRouterNavServiceProvider::class);
        /**
         * @var SourcesLocator $sourceLocator
         */
        $sourceLocator = app(SourcesLocator::class);

        return self::autoAddDataProviderKeys($sourceLocator->getRenderPageList());
    }
}
