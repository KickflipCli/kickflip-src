<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\NpmFetcher;

use Illuminate\Support\Facades\File;
use Kickflip\SiteBuilder\NpmFetcher;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\Feature\BaseFeatureTestCase;
use KickflipMonoTests\ReflectionHelpers;

use function dirname;

use const DIRECTORY_SEPARATOR;

class NpmFetcherBaseFeatureTest extends BaseFeatureTestCase
{
    use DataProviderHelpers;
    use ReflectionHelpers;

    public function setUp(): void
    {
        parent::setUp();
        (new NpmFetcher([]))->removeAndCleanNodeModules();
    }

    public function tearDown(): void
    {
        (new NpmFetcher([]))->removeAndCleanNodeModules();
        parent::tearDown();
    }

    public function testVerifyClassExists()
    {
        self::assertClassExists(NpmFetcher::class);
        self::assertInstanceOf(NpmFetcher::class, new NpmFetcher([]));
    }

    public function testVerifyNpmFetcherMethods()
    {
        $npmFetcher = new NpmFetcher();
        self::assertInstanceOf(NpmFetcher::class, $npmFetcher);
        // Verify methods
        self::assertIsString($npmFetcher->getProjectRootDirectory());
        self::assertEquals(dirname(__FILE__, 4), $npmFetcher->getProjectRootDirectory());
        self::assertIsBool($npmFetcher->isNpmUsedByProject());
        self::assertFalse($npmFetcher->isNpmUsedByProject());
        self::assertIsBool($npmFetcher->isRequired());
        self::assertFalse($npmFetcher->isRequired());
        self::assertIsBool($npmFetcher->isDownloaded());
        self::assertFalse($npmFetcher->isDownloaded());
    }

    public function testVerifyNpmFetcherMethodsWhenInstalled()
    {
        $npmFetcher = new NpmFetcher();
        $npmFetcher->installPackage('shiki');
        self::assertInstanceOf(NpmFetcher::class, $npmFetcher);
        // Verify methods
        self::assertIsBool($npmFetcher->isNpmUsedByProject());
        self::assertFalse($npmFetcher->isNpmUsedByProject());
        self::assertIsBool($npmFetcher->isRequired());
        self::assertTrue($npmFetcher->isRequired());
        self::assertIsBool($npmFetcher->isDownloaded());
        self::assertTrue($npmFetcher->isDownloaded());
    }

    public function testCanReproduceBugsInGithubActions()
    {
        // Initialize shiki npm state when bug happens...
        $npmFetcher = new NpmFetcher();
        $npmFetcher->installPackage('shiki');
        $rootPath = $npmFetcher->getProjectRootDirectory();
        unset($npmFetcher);
        if (File::isFile($rootPath . DIRECTORY_SEPARATOR . 'package.json')) {
            File::delete($rootPath . DIRECTORY_SEPARATOR . 'package.json');
        }

        // After file env matches the bug create a new fetcher
        $npmFetcher = new NpmFetcher();
        self::assertIsBool($npmFetcher->isNpmUsedByProject());
        self::assertFalse($npmFetcher->isNpmUsedByProject());
        self::assertIsBool($npmFetcher->isRequired());
        self::assertTrue($npmFetcher->isRequired());
        self::assertIsBool($npmFetcher->isRequiredPackage('shiki'));
        self::assertFalse($npmFetcher->isRequiredPackage('shiki'));
        self::assertIsBool($npmFetcher->isRequiredPackageLock('shiki'));
        self::assertTrue($npmFetcher->isRequiredPackageLock('shiki'));
        self::assertIsBool($npmFetcher->isDownloaded());
        self::assertTrue($npmFetcher->isDownloaded());
    }
}
