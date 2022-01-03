<?php

declare(strict_types=1);

namespace KickflipMonoTests\DocsFeature;

use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\UrlGenerator;
use Kickflip\SiteBuilder\SourcesLocator;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\DocsTestCase;
use KickflipMonoTests\ReflectionHelpers;

use function app;
use function count;

class UrlRoutesTest extends DocsTestCase
{
    use DataProviderHelpers;
    use ReflectionHelpers;

    public function testEnsureUrlGeneratorSessionResolverIsNull()
    {
        /**
         * @var UrlGenerator $url
         */
        $url = app('url');
        $results = self::reflectionCallMethod($url, 'getSession');
        self::assertNull($results);
    }

    public function testUrlGeneratorKeyResolverConfigs()
    {
        /**
         * @var UrlGenerator $url
         */
        $url = app('url');
        $keyResolver = self::assertHasNonPublicProperty($url, 'keyResolver');
        self::assertIsCallable($keyResolver);
        $value = $keyResolver();
        self::assertIsString($value);
        self::assertStringStartsWith('base64:', $value);
        // Assert keys change
        $key1 = $keyResolver();
        $key2 = $keyResolver();
        self::assertStringStartsWith('base64:', $key1);
        self::assertStringStartsWith('base64:', $key2);
        self::assertNotEquals($key1, $key2);
        self::assertNotEquals($key2, $keyResolver());
    }

    public function testCheckUrlGeneratorRebindsRoutes()
    {
        /**
         * @var UrlGenerator $url
         */
        $url = app('url');
        /**
         * @var RouteCollection $initialRoutes
         */
        $initialRoutes = clone self::assertHasNonPublicProperty($url, 'routes');
        self::assertInstanceOf(RouteCollection::class, $initialRoutes);
        self::assertCount(0, $initialRoutes->getRoutes());
        // This will force routes to be registered...
        app(SourcesLocator::class);
        $updatedRoutes = clone self::assertHasNonPublicProperty($url, 'routes');
        self::assertInstanceOf(RouteCollection::class, $updatedRoutes);
        self::assertCount(count(app(SourcesLocator::class)->getRenderPageList()), $updatedRoutes->getRoutes());
        // Now that the global routes was updated lets override it with the empty clone...
        app()->instance('routes', $initialRoutes);
        $reboundRoutes = clone self::assertHasNonPublicProperty($url, 'routes');
        self::assertInstanceOf(RouteCollection::class, $reboundRoutes);
        self::assertCount(0, $reboundRoutes->getRoutes());
    }
}
