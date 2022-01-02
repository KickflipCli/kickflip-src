<?php

declare(strict_types=1);

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

use Illuminate\Support\Facades\File;
use Illuminate\Support\HtmlString;
use Kickflip\KickflipHelper;
use Kickflip\Models\PageData;
use Kickflip\Models\SourcePageMetaData;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

uses(KickflipMonoTests\TestCase::class)->in('Feature');
uses(KickflipMonoTests\DocsTestCase::class)->in('DocsFeature');

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

expect()->extend('reflectExpectProperty', function (string $propertyName) {
    /**
     * @var \Pest\Expectation $this
     * @var class-string|object $class
     */
    $class = $this->value;
    $reflectionClass = new \ReflectionClass($class);
    $property = $reflectionClass->getProperty($propertyName);
    $property->setAccessible(true);
    return $this->and($property->getValue($class));
});

expect()->extend('reflectCallMethod', function (string $method) {
    /**
     * @var \Pest\Expectation $this
     * @var class-string|object $class
     */
    $class = $this->value;
    $reflectionClass = new \ReflectionClass($class);
    $reflectionMethod = $reflectionClass->getMethod($method);
    $reflectionMethod->setAccessible(true);
    return $this->and($reflectionMethod->invoke($class));
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

function getTestPageData(int $index = 0): PageData
{
    // Fetch a single Symfony SplFileInfo object
    $splFileInfo = File::files(__DIR__ . '/sources/')[$index];
    // Create a SourcePageMetaData object
    $sourcePageMetaData = SourcePageMetaData::fromSplFileInfo($splFileInfo);
    // Parse out the frontmatter page meta data
    $frontMatterData = KickflipHelper::getFrontMatterParser()
            ->parse(file_get_contents($sourcePageMetaData->getFullPath()))
            ->getFrontMatter() ?? [];
    // Create a PageData object
    return PageData::make($sourcePageMetaData, $frontMatterData);
}

function getNodeVersion(): string
{
    $command = [
        (new ExecutableFinder)->find('node', 'node', [
            '/usr/local/bin',
            '/opt/homebrew/bin',
        ]),
        '--version',
    ];

    $process = new Process(
        command: $command,
        cwd: getcwd(),
        timeout: null,
    );
    $process->run();

    if (! $process->isSuccessful()) {
        throw new ProcessFailedException($process);
    }
    return trim($process->getOutput());
}
