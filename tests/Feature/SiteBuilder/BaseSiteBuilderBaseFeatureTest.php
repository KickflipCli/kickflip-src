<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\SiteBuilder;

use Kickflip\KickflipHelper;
use Kickflip\Models\PageData;
use Kickflip\SiteBuilder\NpmFetcher;
use Kickflip\SiteBuilder\SiteBuilder;
use Kickflip\SiteBuilder\SourcesLocator;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\Feature\BaseFeatureTestCase;

use function app;
use function view;

abstract class BaseSiteBuilderBaseFeatureTest extends BaseFeatureTestCase
{
    use DataProviderHelpers;

    protected static ?string $buildEnv;
    protected static bool $prettyUrls = true;

    public function setUp(): void
    {
        parent::setUp();
        // Emulate steps done to prepare things in the build command....
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
        // Init SiteBuilder to boot needed services...
        new SiteBuilder();
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
        KickflipHelper::config()->set('page', $page);
        $view = view($page->source->getName(), [
            'page' => $page,
        ]);
        self::assertMatchesHtmlSnapshot(self::stripMixIdsFromHtml($view->render()));
        KickflipHelper::config()->set('page', null);
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

        return self::autoAddDataProviderKeys($sourceLocator->getRenderPageList());
    }
}
