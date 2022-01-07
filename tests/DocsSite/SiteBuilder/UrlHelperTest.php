<?php

declare(strict_types=1);

namespace KickflipMonoTests\DocsSite\SiteBuilder;

use Kickflip\SiteBuilder\SourcesLocator;
use Kickflip\SiteBuilder\UrlHelper;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\ReflectionHelpers;
use KickflipMonoTests\TestCase;

use function app;
use function dirname;

class UrlHelperTest extends TestCase
{
    use DataProviderHelpers;
    use ReflectionHelpers;

    /**
     * @dataProvider sourceFilePathHelperDataProvider
     */
    public function testSourceFilePathHelper(string $routeName, string $expected): void
    {
        $filePath = UrlHelper::sourceFilePath($routeName);
        self::assertEquals(
            self::agnosticPath(dirname(__DIR__, 3) . "/packages/kickflip/source/{$expected}"),
            $filePath,
        );
    }

    /**
     * @return array<string, array<array-key, string[]>>
     */
    public function sourceFilePathHelperDataProvider(): array
    {
        $this->refreshApplication();
        app(SourcesLocator::class);

        return $this->autoAddDataProviderKeys([
            ['index', 'index.md'],
            ['404', '404.blade.php'],
        ]);
    }
}
