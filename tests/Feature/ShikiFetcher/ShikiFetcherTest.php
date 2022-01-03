<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\ShikiFetcher;

use Illuminate\Support\Facades\File;
use Kickflip\SiteBuilder\ShikiNpmFetcher;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\ReflectionHelpers;
use KickflipMonoTests\TestCase;

use function dirname;

class ShikiFetcherTest extends TestCase
{
    use DataProviderHelpers;
    use ReflectionHelpers;

    public function setUp(): void
    {
        parent::setUp();
        (new ShikiNpmFetcher())->removeShikiAndNodeModules();
    }

    public function tearDown(): void
    {
        (new ShikiNpmFetcher())->removeShikiAndNodeModules();
        parent::tearDown();
    }

    public function testVerifyClassExists()
    {
        self::assertClassExists(ShikiNpmFetcher::class);
        self::assertInstanceOf(ShikiNpmFetcher::class, new ShikiNpmFetcher());
    }

    public function testVerifyShikiNpmFetcherMethods()
    {
        $shikiFetcher = new ShikiNpmFetcher();
        self::assertInstanceOf(ShikiNpmFetcher::class, $shikiFetcher);
        // Verify methods
        self::assertIsString($shikiFetcher->getProjectRootDirectory());
        self::assertEquals(dirname(__FILE__, 4), $shikiFetcher->getProjectRootDirectory());
        self::assertIsBool($shikiFetcher->isNpmUsedByProject());
        self::assertFalse($shikiFetcher->isNpmUsedByProject());
        self::assertIsBool($shikiFetcher->isShikiRequired());
        self::assertFalse($shikiFetcher->isShikiRequired());
        self::assertIsBool($shikiFetcher->isShikiDownloaded());
        self::assertFalse($shikiFetcher->isShikiDownloaded());
    }

    public function testVerifyShikiNpmFetcherMethodsWhenInstalled()
    {
        $shikiFetcher = new ShikiNpmFetcher();
        $shikiFetcher->installShiki();
        self::assertInstanceOf(ShikiNpmFetcher::class, $shikiFetcher);
        // Verify methods
        self::assertIsBool($shikiFetcher->isNpmUsedByProject());
        self::assertFalse($shikiFetcher->isNpmUsedByProject());
        self::assertIsBool($shikiFetcher->isShikiRequired());
        self::assertTrue($shikiFetcher->isShikiRequired());
        self::assertIsBool($shikiFetcher->isShikiDownloaded());
        self::assertTrue($shikiFetcher->isShikiDownloaded());
    }

    public function testCanReproduceBugsInGithubActions()
    {
        // Initialize shiki npm state when bug happens...
        $shikiFetcher = new ShikiNpmFetcher();
        $shikiFetcher->installShiki();
        $rootPath = $shikiFetcher->getProjectRootDirectory();
        unset($shikiFetcher);
        if (File::isFile($rootPath . '/package.json')) {
            File::delete($rootPath . '/package.json');
        }

        // After file env matches the bug create a new fetcher
        $shikiFetcher = new ShikiNpmFetcher();
        self::assertIsBool($shikiFetcher->isNpmUsedByProject());
        self::assertFalse($shikiFetcher->isNpmUsedByProject());
        self::assertIsBool($shikiFetcher->isShikiRequired());
        self::assertTrue($shikiFetcher->isShikiRequired());
        self::assertIsBool($shikiFetcher->isShikiRequiredPackage());
        self::assertFalse($shikiFetcher->isShikiRequiredPackage());
        self::assertIsBool($shikiFetcher->isShikiRequiredPackageLock());
        self::assertTrue($shikiFetcher->isShikiRequiredPackageLock());
        self::assertIsBool($shikiFetcher->isShikiDownloaded());
        self::assertTrue($shikiFetcher->isShikiDownloaded());
    }
}
