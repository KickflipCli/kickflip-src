<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\SiteBuilder;

use Kickflip\KickflipHelper;
use Kickflip\Models\PageData;
use Kickflip\SiteBuilder\ShikiNpmFetcher;
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
        $shikiNpmFetcher = app(ShikiNpmFetcher::class);
        if (!$shikiNpmFetcher->isShikiDownloaded()) {
            $shikiNpmFetcher->installShiki();
        }
        // Init SiteBuilder to boot needed services...
        new SiteBuilder();
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
