<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\NpmFetcher;

use Illuminate\Support\Str;
use Kickflip\SiteBuilder\NpmFetcher;
use KickflipMonoTests\Feature\BaseFeatureTestCase;
use KickflipMonoTests\NpmHelpers;

use function filter_var;

use const FILTER_VALIDATE_INT;

class NpmFetcherInstallBaseFeatureTest extends BaseFeatureTestCase
{
    use NpmHelpers;

    public function setUp(): void
    {
        parent::setUp();
        (new NpmFetcher())->removeAndCleanNodeModules();
    }

    public function tearDown(): void
    {
        (new NpmFetcher())->removeAndCleanNodeModules();
        parent::tearDown();
    }

    public function testInstallingShikiAndNodeModules()
    {
        $npmFetcher = new NpmFetcher();
        // preinstall tests
        self::assertFileDoesNotExist($npmFetcher->getProjectRootDirectory() . self::agnosticPath('/package.json'));
        // phpcs:ignore
        self::assertFileDoesNotExist($npmFetcher->getProjectRootDirectory() . self::agnosticPath('/package-lock.json'));
        // phpcs:ignore
        self::assertDirectoryDoesNotExist($npmFetcher->getProjectRootDirectory() . self::agnosticPath('/node_modules'));

        $npmFetcher->installPackage('shiki');

        // post install tests
        if (filter_var(Str::of($this->getNodeVersion())->before('.')->after('v'), FILTER_VALIDATE_INT) >= 15) {
            self::assertFileIsReadable($npmFetcher->getProjectRootDirectory() . self::agnosticPath('/package.json'));
        }
        self::assertFileIsWritable($npmFetcher->getProjectRootDirectory() . self::agnosticPath('/package-lock.json'));
        self::assertDirectoryIsWritable($npmFetcher->getProjectRootDirectory() . self::agnosticPath('/node_modules'));
    }
}
