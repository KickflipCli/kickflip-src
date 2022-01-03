<?php

namespace KickflipMonoTests;

use Illuminate\Support\HtmlString;
use ReflectionClass;
use PHPUnit\Framework\Assert;

trait ReflectionHelpers
{
    public static function assertClassExists(string $className)
    {
        Assert::assertIsString($className);
        Assert::assertTrue(class_exists($className));
    }

    public static function isHtmlStringOf(string $expected, $actual) {
        self::assertInstanceOf(HtmlString::class, $actual);
        $castString = (string) $actual;
        self::assertEquals($expected, $castString);
    }

    /**
     * @param object|class-string $objectOrClassName
     * @param string              $property
     *
     * @return mixed
     */
    public static function assertHasNonPublicProperty(object|string $objectOrClassName, string $property)
    {
        $reflectionClass = new ReflectionClass($objectOrClassName);
        Assert::assertTrue($reflectionClass->hasProperty($property));
        $reflectionProperty = $reflectionClass->getProperty($property);
        if ($reflectionProperty->isStatic()) {
            return $reflectionProperty->getValue();
        }
        return $reflectionProperty->getValue($objectOrClassName);
    }

    public static function reflectionGetPropertyValue(object|string $objectOrClassName, string $propertyName)
    {
        $reflectionClass = new \ReflectionClass($objectOrClassName);
        $property = $reflectionClass->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($reflectionClass);
    }

    public static function reflectionCallMethod(object|string $objectOrClassName, string $method)
    {
        $reflectionClass = new \ReflectionClass($objectOrClassName);
        $reflectionMethod = $reflectionClass->getMethod($method);
        $reflectionMethod->setAccessible(true);
        if ($reflectionMethod->isStatic()) {
            return $reflectionMethod->invoke(null);
        }
        return $reflectionMethod->invoke($objectOrClassName);
    }

    /**
     * @param object|class-string   $object
     * @param string                $name
     * @param null|mixed            $value
     *
     * @return void
     */
    public static function assertHasProperty(object|string $object, string $name, $value = null)
    {
        Assert::assertTrue(property_exists($object, $name));

        if (func_num_args() > 2) {
            Assert::assertIsObject($object);
            /* @phpstan-ignore-next-line */
            Assert::assertEquals($value, $object->{$name});
        }
    }

    /**
     * Asserts that the value contains the provided properties $names.
     *
     * @param iterable<array-key, string> $names
     */
    public static function assertHasProperties(object$object, iterable $names)
    {
        foreach ($names as $name) {
            self::assertHasProperty($object, $name);
        }
    }
}
