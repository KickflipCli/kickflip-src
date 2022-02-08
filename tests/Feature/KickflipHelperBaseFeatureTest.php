<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature;

use Kickflip\KickflipHelper;
use Kickflip\Models\PageData;
use Kickflip\SiteBuilder\SourcesLocator;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\PlatformAgnosticHelpers;
use KickflipMonoTests\ReflectionHelpers;

use function app;
use function class_exists;

class KickflipHelperBaseFeatureTest extends BaseFeatureTestCase
{
    use DataProviderHelpers;
    use ReflectionHelpers;
    use PlatformAgnosticHelpers;

    public function testVerifyHelperAlias(): void
    {
        self::assertTrue(class_exists('\KickflipHelper'));
    }

    /**
     * @dataProvider pageDataProvider
     */
    public function testPageRouteNameHelper(PageData $page)
    {
        self::assertEquals($page->source->getRouteName(), KickflipHelper::pageRouteName($page));
    }

    /**
     * @return array<string, array<array-key, PageData>>
     */
    public function pageDataProvider(): array
    {
        $this->refreshApplication();

        return self::autoAddDataProviderKeys(app(SourcesLocator::class)->getRenderPageList());
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
            ['/half-life/blackmesa/', 'half-life/blackmesa/'],
            ['/hello-world/', 'hello-world/'],
            ['/half-life/blackmesa.html', 'half-life/blackmesa.html'],
            ['http://kickflip.test/half-life/blackmesa/', 'half-life/blackmesa/'],
            ['http://kickflip.test/half-life/blackmesa.html', 'half-life/blackmesa.html'],
        ]);
    }

    /**
     * @dataProvider urlFromSourceProvider
     */
    public function testUrlFromSource(string $input, string $expected): void
    {
        self::assertEquals($expected, KickflipHelper::urlFromSource($input));
    }

    /**
     * @return array<array-key, array<array-key, string[]>>
     */
    public function urlFromSourceProvider(): array
    {
        return $this->autoAddDataProviderKeys([
            ['index', 'http://kickflip.test/'],
            ['404', 'http://kickflip.test/404'],
        ]);
    }

    public function testRelativeUrl(): void
    {
        $config = KickflipHelper::config();
        $config->set('http://kickflip.test');
        self::assertEquals('test/', \KickflipHelper::relativeUrl('http://kickflip.test/test/'));
        $config->set('http://kickflip.test/');
        self::assertEquals('test/', \KickflipHelper::relativeUrl('http://kickflip.test/test/'));
        $config->set('http://kickflip.test/subdir/');
        self::assertEquals('subdir/test/', \KickflipHelper::relativeUrl('http://kickflip.test/subdir/test/'));
        self::assertEquals('subdir/test/', \KickflipHelper::relativeUrl('/subdir/test/'));
        $config->set('http://kickflip.test');
        self::assertEquals('subdir/test/', \KickflipHelper::relativeUrl('http://kickflip.test/subdir/test/'));
        self::assertEquals('subdir/test/', \KickflipHelper::relativeUrl('/subdir/test/'));
    }
}
