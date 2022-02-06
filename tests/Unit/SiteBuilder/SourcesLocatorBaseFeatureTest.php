<?php

declare(strict_types=1);

namespace KickflipMonoTests\Unit\SiteBuilder;

use Kickflip\SiteBuilder\SourcesLocator;
use KickflipMonoTests\ReflectionHelpers;
use PHPUnit\Framework\TestCase;
use RuntimeException;

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
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('A facade root has not been set.');
        new SourcesLocator(dirname(__DIR__, 2) . '/sources');
    }
}
