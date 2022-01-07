<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature;

use Kickflip\KickflipHelper;
use Kickflip\Models\PageData;
use Kickflip\SiteBuilder\SourcesLocator;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\PlatformAgnosticHelpers;
use KickflipMonoTests\ReflectionHelpers;
use KickflipMonoTests\TestCase;

use function app;

class KickflipHelperTest extends TestCase
{
    use DataProviderHelpers;
    use ReflectionHelpers;
    use PlatformAgnosticHelpers;

    /**
     * @dataProvider pageDataProvider
     */
    public function testPageRouteNameHelper(PageData $page)
    {
        self::assertEquals($page->getRouteName(), KickflipHelper::pageRouteName($page));
    }

    /**
     * @return array<string, array<array-key, PageData>>
     */
    public function pageDataProvider(): array
    {
        $this->refreshApplication();

        return self::autoAddDataProviderKeys(app(SourcesLocator::class)->getRenderPageList());
    }
}
