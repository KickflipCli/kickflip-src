<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

use Illuminate\Support\HtmlString;

uses(KickflipMonoTests\TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('isEnumValue', function (string $enum, $actual) {
    /**
     * @var \Pest\Expectation $this
     */
    return $this->toBeInstanceOf($enum)
            ->and($this->value->value)->toBeScalar()->toBe($actual);
});

expect()->extend('isHtmlStringOf', function (string $actual) {
    /**
     * @var \Pest\Expectation $this
     */
    $castString = (string) $this->value;
    return $this->toBeInstanceOf(HtmlString::class)
        ->and($castString)->toBeString()->toBe($actual);
});

expect()->extend('reflectHasProperty', function (string $property) {
    /**
     * @var \Pest\Expectation $this
     * @var class-string|object $class
     */
    $class = $this->value;
    $reflectionClass = new ReflectionClass($class);
    return $this->and($reflectionClass->hasProperty($property))->toBeTrue();
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}
