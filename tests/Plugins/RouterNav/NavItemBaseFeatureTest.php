<?php

declare(strict_types=1);

namespace KickflipMonoTests\Plugins\RouterNav;

use Illuminate\Contracts\Container\BindingResolutionException;
use Kickflip\RouterNavPlugin\Models\NavItem;
use Kickflip\SiteBuilder\SiteBuilder;
use Kickflip\SiteBuilder\SourcesLocator;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\Feature\BaseFeatureTestCase;

use function app;
use function route;

class NavItemBaseFeatureTest extends BaseFeatureTestCase
{
    use DataProviderHelpers;

    public const ENV = 'dev';

    public function setUp(): void
    {
        parent::setUp();
        $this->prepareEnv();
    }

    /**
     * This is necessary to allow NavItems to look up route names.
     */
    protected function prepareEnv(): void
    {
        SiteBuilder::includeEnvironmentConfig(static::ENV);
        SiteBuilder::updateBuildPaths(static::ENV);
        SiteBuilder::updateAppUrl();
        app(SourcesLocator::class);
    }

    public function testRouteHelperFailsWithoutPlugin(): void
    {
        $this->expectException(BindingResolutionException::class);
        $this->expectExceptionMessage('Target class [url] does not exist.');
        NavItem::make('Home', route('index'));
    }
}
