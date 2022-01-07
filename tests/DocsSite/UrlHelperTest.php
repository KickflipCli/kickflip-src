<?php

declare(strict_types=1);

namespace KickflipMonoTests\DocsSite;

use Kickflip\SiteBuilder\SourcesLocator;
use Kickflip\SiteBuilder\UrlHelper;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\DocsTestCase;
use KickflipMonoTests\ReflectionHelpers;

use function app;
use function dirname;

class UrlHelperTest extends DocsTestCase
{
    use DataProviderHelpers;
    use ReflectionHelpers;

    /**
     * @dataProvider sourceFilePathHelperDataProvider
     */
    public function testSourceFilePathHelper(string $routeName, string $expected)
    {
        $filePath = UrlHelper::sourceFilePath($routeName);
        self::assertEquals(
            dirname(__DIR__, 2) . self::agnosticPath("/packages/kickflip-docs/source/{$expected}"),
            $filePath,
        );
    }

    /**
     * @return array<string, array<array-key, string[]>>
     */
    public function sourceFilePathHelperDataProvider()
    {
        $this->refreshApplication();
        app(SourcesLocator::class);

        return $this->autoAddDataProviderKeys([
            ['index', 'index.md.blade.php'],
            ['404', '404.blade.php'],
            ['docs.getting-started', 'docs/getting-started.md.blade.php'],
            ['docs.template-variables-site', 'docs/template-variables-site.md.blade.php'],
        ]);
    }
}
