<?php

declare(strict_types=1);

namespace KickflipMonoTests\DocsSite\SiteBuilder;

use Kickflip\Events\SiteBuildStarted;
use Kickflip\Models\PageData;
use Kickflip\SiteBuilder\NpmFetcher;
use Kickflip\SiteBuilder\SiteBuilder;
use Kickflip\SiteBuilder\SourcesLocator;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\DocsSite\DocsTestCase;

use function app;
use function array_slice;
use function view;

abstract class BaseSiteBuilderTest extends DocsTestCase
{
    use DataProviderHelpers;

    protected static ?string $buildEnv;
    protected static bool $prettyUrls = true;

    public function setUp(): void
    {
        parent::setUp();
        $this->app->get('kickflipCli')->set('prettyUrls', static::$prettyUrls);
        SiteBuilder::includeEnvironmentConfig(static::$buildEnv);
        SiteBuilder::updateBuildPaths(static::$buildEnv);
        SiteBuilder::updateAppUrl();
        /**
         * @var NpmFetcher $npmFetcher
         */
        $npmFetcher = app(NpmFetcher::class);
        if (!$npmFetcher->isDownloaded()) {
            foreach ($npmFetcher->packages() as $package) {
                $npmFetcher->installPackage($package);
            }
        }
        SiteBuildStarted::dispatch();
    }

    public function tearDown(): void
    {
        /**
         * @var NpmFetcher $npmFetcher
         */
        $npmFetcher = app(NpmFetcher::class);
        if ($npmFetcher->isDownloaded()) {
            $npmFetcher->removeAndCleanNodeModules();
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
        /**
         * @var SourcesLocator $sourceLocator
         */
        $sourceLocator = app(SourcesLocator::class);
        $testSlice = array_slice($sourceLocator->getRenderPageList(), 0, 4);

        return self::autoAddDataProviderKeys($testSlice);
    }
}
