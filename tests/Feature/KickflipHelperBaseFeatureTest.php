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

class KickflipHelperBaseFeatureTest extends BaseFeatureTestCase
{
    use DataProviderHelpers;
    use ReflectionHelpers;
    use PlatformAgnosticHelpers;

    /**
     * @dataProvider pageDataProvider
     */
    public function testPageRouteNameHelper(PageData $page)
    {
        self::assertEquals($page->source->getName(), KickflipHelper::pageRouteName($page));
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
}
