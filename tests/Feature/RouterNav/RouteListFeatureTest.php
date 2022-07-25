<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\RouterNav;

use Kickflip\Providers\KickflipRouterNavServiceProvider;
use Kickflip\SiteBuilder\SiteBuilder;
use Kickflip\SiteBuilder\SourcesLocator;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\Feature\BaseFeatureTestCase;
use KickflipMonoTests\ReflectionHelpers;
use LaravelZero\Framework\Kernel;

use function app;

class RouteListFeatureTest extends BaseFeatureTestCase
{
    use DataProviderHelpers;
    use ReflectionHelpers;

    protected const ENV = 'production';

    public function setUp(): void
    {
        parent::setUp();
        $this->prepareProdEnv();
    }

    protected function prepareProdEnv()
    {
        $this->app->register(KickflipRouterNavServiceProvider::class, true);
        SiteBuilder::includeEnvironmentConfig(static::ENV);
        SiteBuilder::updateBuildPaths(static::ENV);
        SiteBuilder::updateAppUrl();
        app(SourcesLocator::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testRouteListCommand(): void
    {
        $artisanKernel = $this->app->get(Kernel::class);
        $artisanKernel->call('route:list');
        $this->assertMatchesTextSnapshot($artisanKernel->output());
    }
}
