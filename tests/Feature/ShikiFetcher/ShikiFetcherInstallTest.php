<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\ShikiFetcher;

use Illuminate\Support\Str;
use Kickflip\SiteBuilder\ShikiNpmFetcher;
use KickflipMonoTests\NpmHelpers;
use KickflipMonoTests\TestCase;

use function filter_var;

use const FILTER_VALIDATE_INT;

class ShikiFetcherInstallTest extends TestCase
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
        self::assertFileDoesNotExist($shikiFetcher->getProjectRootDirectory() . '/package.json');
        self::assertFileDoesNotExist($shikiFetcher->getProjectRootDirectory() . '/package-lock.json');
        self::assertDirectoryDoesNotExist($shikiFetcher->getProjectRootDirectory() . '/node_modules');

        $shikiFetcher->installShiki();

        // post install tests
        if (filter_var(Str::of($this->getNodeVersion())->before('.')->after('v'), FILTER_VALIDATE_INT) >= 15) {
            self::assertFileIsReadable($shikiFetcher->getProjectRootDirectory() . '/package.json');
        }
        self::assertFileIsWritable($shikiFetcher->getProjectRootDirectory() . '/package-lock.json');
        self::assertDirectoryIsWritable($shikiFetcher->getProjectRootDirectory() . '/node_modules');
    }
}
