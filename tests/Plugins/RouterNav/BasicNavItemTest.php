<?php

declare(strict_types=1);

namespace KickflipMonoTests\Plugins\RouterNav;

use Kickflip\RouterNavPlugin\Models\NavItem;
use KickflipMonoTests\ReflectionHelpers;
use PHPUnit\Framework\TestCase;

class BasicNavItemTest extends TestCase
{
    use ReflectionHelpers;

    public function testVerifyClassExists(): void
    {
        self::assertClassExists(NavItem::class);
    }
}
