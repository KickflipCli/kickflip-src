<?php

declare(strict_types=1);

namespace KickflipMonoTests\Unit\SiteBuilder;

use Kickflip\SiteBuilder\SourcesLocator;
use KickflipMonoTests\ReflectionHelpers;
use KickflipMonoTests\Unit\BaseUnitTestCase;
use RuntimeException;

use function dirname;

class SourcesLocatorBaseFeatureTest extends BaseUnitTestCase
{
    use ReflectionHelpers;

    public function testCanVerifyClassExists()
    {
        self::assertClassExists(SourcesLocator::class);
    }

    public function testExpectsAnException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Cannot access Kickflip state before initialized.');
        new SourcesLocator(dirname(__DIR__, 2) . '/sources');
    }
}
