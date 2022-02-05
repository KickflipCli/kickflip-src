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
     * @after
     */
    protected function cleanUp(): void
    {
        KickflipHelper::basePath('');
    }

    public function testDefaultBasePath(): void
    {
        $basePath = KickflipHelper::basePath();
        self::assertIsString($basePath);
        self::assertEquals(dirname(__DIR__, 2), $basePath);
        $basePath = KickflipHelper::basePath(__DIR__ . '/../../packages/kickflip');
        self::assertEquals(dirname(__DIR__, 2) . static::agnosticPath('/packages/kickflip'), $basePath);
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
            [null, ''],
            ['./packages/kickflip', '/packages/kickflip'],
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
}
