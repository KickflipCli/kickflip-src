<?php

declare(strict_types=1);

namespace KickflipMonoTests\Unit\SiteBuilder;

use Illuminate\Contracts\Container\BindingResolutionException;
use Kickflip\SiteBuilder\SourcesLocator;
use KickflipMonoTests\ReflectionHelpers;
use PHPUnit\Framework\TestCase;

use function dirname;

class SourcesLocatorBaseFeatureTest extends TestCase
{
    use ReflectionHelpers;

    public function testCanVerifyClassExists()
    {
        self::assertClassExists(SourcesLocator::class);
    }

    public function testExpectsAnException()
    {
        self::expectException(BindingResolutionException::class);
        self::expectExceptionMessage('Target class [kickflipCli] does not exist.');
        new SourcesLocator(dirname(__DIR__, 2) . '/sources');
    }
}
