<?php

declare(strict_types=1);

namespace KickflipMonoTests\DocsSite;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Kickflip\Enums\CliStateDirPaths;
use Kickflip\KickflipHelper;
use Kickflip\SiteBuilder\ShikiNpmFetcher;
use function is_dir;
use function mkdir;

class BuildCommandProdTest extends DocsTestCase
{
    private const BUILD_ENV = 'production';

    public function setUp(): void
    {
        parent::setUp();
        (new ShikiNpmFetcher())->removeShikiAndNodeModules();
        $buildPath = (string) Str::of(
            KickflipHelper::namedPath(CliStateDirPaths::BuildDestination),
        )->replaceEnv(self::BUILD_ENV);
        if (is_dir($buildPath)) {
            File::deleteDirectory($buildPath);
        }
    }

    public function tearDown(): void
    {
        (new ShikiNpmFetcher())->removeShikiAndNodeModules();
        $buildPath = (string) Str::of(
            KickflipHelper::namedPath(CliStateDirPaths::BuildDestination),
        )->replaceEnv(self::BUILD_ENV);
        if (is_dir($buildPath)) {
            File::deleteDirectory($buildPath);
        }
        parent::tearDown();
    }

    public function testBuildCommand()
    {
        $this->artisan('build ' . self::BUILD_ENV)
            ->assertSuccessful();
    }

    public function testSuccessfulFakeDirtyBuild()
    {
        $buildPath = (string) Str::of(
            KickflipHelper::namedPath(CliStateDirPaths::BuildDestination),
        )->replaceEnv(self::BUILD_ENV);
        mkdir($buildPath);

        $this->artisan('build ' . self::BUILD_ENV)
            ->expectsConfirmation('Overwrite "' . $buildPath . '"? ', 'yes')
            ->assertSuccessful();
    }

    public function testDeniedFakeDirtyBuild()
    {
        $buildPath = (string) Str::of(
            KickflipHelper::namedPath(CliStateDirPaths::BuildDestination),
        )->replaceEnv(self::BUILD_ENV);
        mkdir($buildPath);

        $this->artisan('build ' . self::BUILD_ENV)
            ->expectsConfirmation('Overwrite "' . $buildPath . '"? ', 'no')
            ->assertFailed();
    }
}
