<?php

declare(strict_types=1);

namespace KickflipMonoTests\Unit;

use Kickflip\KickflipHelper;
use Kickflip\Logger;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Throwable;

abstract class BaseUnitTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        try {
            KickflipHelper::getKickflipState();
            $reflectionObject = new ReflectionClass(KickflipHelper::class);
            $propertyReflection = $reflectionObject->getProperty('kickflipState');
            $propertyReflection->setAccessible(true);
            $propertyReflection->setValue(null);
        } catch (Throwable $throws) {
        }

        try {
            Logger::getKickflipTimings();
            $reflectionObject = new ReflectionClass(Logger::class);
            $propertyReflection = $reflectionObject->getProperty('kickflipTimings');
            $propertyReflection->setAccessible(true);
            $propertyReflection->setValue(null);
        } catch (Throwable $throws) {
        }
    }
}
