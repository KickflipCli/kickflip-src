<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\ShikiFetcher;

use Illuminate\Support\Str;
use Kickflip\SiteBuilder\ShikiNpmFetcher;
use KickflipMonoTests\Feature\BaseFeatureTestCase;
use KickflipMonoTests\NpmHelpers;

use function filter_var;

use const FILTER_VALIDATE_INT;

class ShikiFetcherInstallBaseFeatureTest extends BaseFeatureTestCase
{
    use NpmHelpers;

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

    public function testInstallingShikiAndNodeModules()
    {
        $shikiFetcher = new ShikiNpmFetcher();
        // preinstall tests
        self::assertFileDoesNotExist($shikiFetcher->getProjectRootDirectory() . self::agnosticPath('/package.json'));
        // phpcs:ignore
        self::assertFileDoesNotExist($shikiFetcher->getProjectRootDirectory() . self::agnosticPath('/package-lock.json'));
        // phpcs:ignore
        self::assertDirectoryDoesNotExist($shikiFetcher->getProjectRootDirectory() . self::agnosticPath('/node_modules'));

        $shikiFetcher->installShiki();

        // post install tests
        if (filter_var(Str::of($this->getNodeVersion())->before('.')->after('v'), FILTER_VALIDATE_INT) >= 15) {
            self::assertFileIsReadable($shikiFetcher->getProjectRootDirectory() . self::agnosticPath('/package.json'));
        }
        self::assertFileIsWritable($shikiFetcher->getProjectRootDirectory() . self::agnosticPath('/package-lock.json'));
        self::assertDirectoryIsWritable($shikiFetcher->getProjectRootDirectory() . self::agnosticPath('/node_modules'));
    }
}
