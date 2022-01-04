<?php

declare(strict_types=1);

namespace KickflipMonoTests\Unit;

use Kickflip\KickflipHelper;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\PlatformAgnosticHelpers;
use KickflipMonoTests\ReflectionHelpers;
use League\CommonMark\Extension\FrontMatter\FrontMatterParserInterface;
use PHPUnit\Framework\TestCase;

use function dirname;

class KickflipHelperTest extends TestCase
{
    use DataProviderHelpers;
    use ReflectionHelpers;
    use PlatformAgnosticHelpers;

    /**
     * @before
     */
    public function setBasePath(): void
    {
        KickflipHelper::basePath(dirname(__DIR__, 2) . '/packages/kickflip-docs');
    }

    public function testDefaultBasePath(): void
    {
        $basePath = KickflipHelper::basePath();
        self::assertIsString($basePath);
        self::assertEquals(dirname(__DIR__, 2) . static::agnosticPath('/packages/kickflip-docs'), $basePath);
    }

    /**
     * @dataProvider basePathProvider
     */
    public function testCustomBasePath(?string $input, string $expected): void
    {
        $basePath = KickflipHelper::basePath($input);
        self::assertIsString($basePath);
        self::assertEquals(dirname(__DIR__, 2) . static::agnosticPath($expected), $basePath);
    }

    /**
     * @return array<array-key, ?string[]>
     */
    public function basePathProvider(): array
    {
        return $this->autoAddDataProviderKeys([
            [null, '/packages/kickflip-docs'],
            ['./', ''],
            ['./packages/kickflip-docs', '/packages/kickflip-docs'],
        ]);
    }

    public function testRootPackagePath(): void
    {
        $rootPackagePath = KickflipHelper::rootPackagePath();
        self::assertIsString($rootPackagePath);
        self::assertEquals(dirname(__DIR__, 2) . static::agnosticPath('/packages/kickflip-cli'), $rootPackagePath);
    }

    /**
     * @dataProvider kebabStringProvider
     */
    public function testHelperToKebab(string $input, string $expected)
    {
        $kebabString = KickflipHelper::toKebab($input);
        self::assertIsString($kebabString);
        self::assertEquals($expected, $kebabString);
    }

    /**
     * @return array<array-key, string[]>
     */
    public function kebabStringProvider(): array
    {
        return $this->autoAddDataProviderKeys([
            ['Hello World', 'hello-world'],
            ['Hello Kickflip!', 'hello-kickflip!'],
            ['Hello World, from kickflip!', 'hello-world,-from-kickflip!'],
        ]);
    }

    public function testGettingFrontmatterParser(): void
    {
        self::assertInstanceOf(FrontMatterParserInterface::class, KickflipHelper::getFrontMatterParser());
    }

    /**
     * @dataProvider leftTrimPathProvider
     */
    public function testHelperLeftTrimPath(string $input, string $expected): void
    {
        $leftTrimString = KickflipHelper::leftTrimPath($input);
        self::assertIsString($leftTrimString);
        self::assertEquals($expected, $leftTrimString);
    }

    /**
     * @return array<array-key, string[]>
     */
    public function leftTrimPathProvider(): array
    {
        return $this->autoAddDataProviderKeys([
            ['hello', 'hello'],
            ['/hello', 'hello'],
            ['/hello/', 'hello/'],
            ['hello/', 'hello/'],
        ]);
    }

    /**
     * @dataProvider rightTrimPathProvider
     */
    public function testHelperRightTrimPath(string $input, string $expected): void
    {
        $rightTrimPath = KickflipHelper::rightTrimPath($input);
        self::assertIsString($rightTrimPath);
        self::assertEquals($expected, $rightTrimPath);
    }

    /**
     * @return array<array-key, string[]>
     */
    public function rightTrimPathProvider(): array
    {
        return $this->autoAddDataProviderKeys([
            ['hello', 'hello'],
            ['/hello', '/hello'],
            ['/hello/', '/hello'],
            ['hello/', 'hello'],
        ]);
    }

    /**
     * @dataProvider trimPathProvider
     */
    public function testHelperTrimPath(string $input, string $expected): void
    {
        $leftTrimString = KickflipHelper::trimPath($input);
        self::assertIsString($leftTrimString);
        self::assertEquals($expected, $leftTrimString);
    }

    /**
     * @return array<array-key, string[]>
     */
    public function trimPathProvider(): array
    {
        return $this->autoAddDataProviderKeys([
            ['hello', 'hello'],
            ['/hello', 'hello'],
            ['/hello/', 'hello'],
            ['hello/', 'hello'],
        ]);
    }

    /**
     * @dataProvider relativeUrlProvider
     */
    public function testHelperRelativeUrl(string $input, string $expected): void
    {
        $leftTrimString = KickflipHelper::relativeUrl($input);
        self::assertIsString($leftTrimString);
        self::assertEquals($expected, $leftTrimString);
    }

    /**
     * @return array<array-key, string[]>
     */
    public function relativeUrlProvider(): array
    {
        return $this->autoAddDataProviderKeys([
            ['http://google.com/half-life/blackmesa/', 'http://google.com/half-life/blackmesa/'],
            ['https://google.com/half-life/blackmesa/', 'https://google.com/half-life/blackmesa/'],
            ['/half-life/blackmesa/', 'half-life/blackmesa'],
            ['/hello-world/', 'hello-world'],
            ['/half-life/blackmesa.html', 'half-life/blackmesa.html'],
        ]);
    }
}
