<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\RouterNav;

use Illuminate\Routing\UrlGenerator;
use Kickflip\SiteBuilder\SiteBuilder;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\Feature\BaseFeatureTestCase;
use KickflipMonoTests\ReflectionHelpers;

use function app;

class SubDirRouteBaseFeatureTest extends BaseFeatureTestCase
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

    /**
     * @dataProvider routeNameProvider
     */
    public function testItProducesRoutesWithSubDirectory(string $routeName, string $expected)
    {
        /**
         * @var UrlGenerator $url
         */
        $url = app('url');
        self::assertIsString($url->route($routeName));
        self::assertEquals($expected, $url->route($routeName));
    }

    /**
     * @return array<string, string[]>
     */
    public function routeNameProvider(): array
    {
        return self::autoAddDataProviderKeys([
            ['index', 'http://kickflip.test/prod'],
            ['404', 'http://kickflip.test/prod/404'],
        ]);
    }
}
