<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\RouterNav;

use Kickflip\SiteBuilder\SiteBuilder;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\Feature\BaseFeatureTestCase;
use KickflipMonoTests\ReflectionHelpers;
use LaravelZero\Framework\Kernel;

use function putenv;

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
        SiteBuilder::includeEnvironmentConfig(static::ENV);
        SiteBuilder::updateBuildPaths(static::ENV);
        SiteBuilder::updateAppUrl();
        $this->initAndFindSources();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testRouteListCommand(): void
    {
        /**
         * @var Kernel $artisanKernel
         */
        $artisanKernel = $this->app->get(Kernel::class);
        putenv('COLUMNS=80'); // Ensure the output is normalized on all systems
        $artisanKernel->call('route:list');
        $this->assertMatchesTextSnapshot($artisanKernel->output());
    }
}
