<?php

declare(strict_types=1);

namespace KickflipMonoTests\DocsSite;

use Kickflip\KickflipHelper;
use Kickflip\Models\PageData;
use Kickflip\SiteBuilder\SourcesLocator;
use function app;

class KickflipHelperTest extends DocsTestCase
{
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
}
