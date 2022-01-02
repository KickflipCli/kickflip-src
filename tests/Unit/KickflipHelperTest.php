<?php

declare(strict_types=1);

namespace KickflipMonoTests\Unit;

use Kickflip\KickflipHelper;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\ReflectionHelpers;
use League\CommonMark\Extension\FrontMatter\FrontMatterParserInterface;
use PHPUnit\Framework\TestCase;

class KickflipHelperTest extends TestCase {
    use DataProviderHelpers, ReflectionHelpers;
    /**
     * @before
     */
    public function setBasePath()
    {
        KickflipHelper::basePath(dirname(__DIR__, 2) . '/packages/kickflip-docs');
    }

    public function testDefaultBasePath()
    {
        $basePath = KickflipHelper::basePath();
        self::assertIsString($basePath);
        self::assertEquals(dirname(__DIR__, 2) . '/packages/kickflip-docs', $basePath);
    }

    /**
     * @dataProvider basePathProvider
     */
    public function testCustomBasePath(?string $input, string $expected)
    {
        $basePath = KickflipHelper::basePath($input);
        self::assertIsString($basePath);
        self::assertEquals(dirname(__DIR__, 2) . $expected, $basePath);
    }

    public function basePathProvider()
    {
        return $this->autoAddDataProviderKeys([
            [null, '/packages/kickflip-docs'],
            ['./', ''],
            ['./packages/kickflip-docs', '/packages/kickflip-docs'],
        ]);
    }

    public function testRootPackagePath()
    {
        $rootPackagePath = KickflipHelper::rootPackagePath();
        self::assertIsString($rootPackagePath);
        self::assertEquals(dirname(__DIR__, 2) . '/packages/kickflip-cli', $rootPackagePath);
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

    public function kebabStringProvider()
    {
        return $this->autoAddDataProviderKeys([
            ['Hello World', 'hello-world'],
            ['Hello Kickflip!', 'hello-kickflip!'],
            ['Hello World, from kickflip!', 'hello-world,-from-kickflip!'],
        ]);
    }

    public function testGettingFrontmatterParser()
    {
        self::assertInstanceOf(FrontMatterParserInterface::class, KickflipHelper::getFrontMatterParser());
    }

    /**
     * @dataProvider leftTrimPathProvider
     */
    public function testHelperLeftTrimPath(string $input, string $expected)
    {
        $leftTrimString = KickflipHelper::leftTrimPath($input);
        self::assertIsString($leftTrimString);
        self::assertEquals($expected, $leftTrimString);
    }

    public function leftTrimPathProvider()
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
    public function testHelperRightTrimPath(string $input, string $expected)
    {
        $rightTrimPath = KickflipHelper::rightTrimPath($input);
        self::assertIsString($rightTrimPath);
        self::assertEquals($expected, $rightTrimPath);
    }

    public function rightTrimPathProvider()
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
    public function testHelperTrimPath(string $input, string $expected)
    {
        $leftTrimString = KickflipHelper::trimPath($input);
        self::assertIsString($leftTrimString);
        self::assertEquals($expected, $leftTrimString);
    }

    public function trimPathProvider()
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
    public function testHelperRelativeUrl(string $input, string $expected)
    {
        $leftTrimString = KickflipHelper::relativeUrl($input);
        self::assertIsString($leftTrimString);
        self::assertEquals($expected, $leftTrimString);
    }

    public function relativeUrlProvider()
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
