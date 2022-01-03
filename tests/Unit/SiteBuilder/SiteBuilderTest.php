<?php

declare(strict_types=1);

namespace KickflipMonoTests\Unit\SiteBuilder;

// We do this to have access to laravel's filesystem facade used by SourcesLocator
use Kickflip\SiteBuilder\SiteBuilder;
use KickflipMonoTests\ReflectionHelpers;
use KickflipMonoTests\TestCase;

class SiteBuilderTest extends TestCase {
    use ReflectionHelpers;

    public function testItCanInstantiateSiteBuilder()
    {
        self::assertClassExists(SiteBuilder::class);
        $siteBuilder = new SiteBuilder();
        self::assertInstanceOf(SiteBuilder::class, $siteBuilder);
        self::assertHasProperty($siteBuilder, 'sourcesLocator');
    }
}
