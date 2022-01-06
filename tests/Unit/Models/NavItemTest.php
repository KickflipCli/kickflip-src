<?php

declare(strict_types=1);

namespace KickflipMonoTests\Unit\Models;

use Kickflip\RouterNavPlugin\Models\NavItem;
use KickflipMonoTests\ReflectionHelpers;
use PHPUnit\Framework\TestCase;

class NavItemTest extends TestCase
{
    use ReflectionHelpers;

    public function testVerifyClassExists(): void
    {
        self::assertClassExists(NavItem::class);
    }
}
