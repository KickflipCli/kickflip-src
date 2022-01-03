<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Kickflip\SiteBuilder\ShikiNpmFetcher;
use KickflipMonoTests\TestCase;

class BuildCommandTest extends TestCase {
    private const BUILD_ENV = 'local';

    public function setUp(): void
    {
        parent::setUp();
        (new ShikiNpmFetcher())->removeShikiAndNodeModules();
        $buildPath = (string) Str::of(\Kickflip\KickflipHelper::namedPath(\Kickflip\Enums\CliStateDirPaths::BuildDestination))->replaceEnv(self::BUILD_ENV);
        if (is_dir($buildPath)) {
            File::deleteDirectory($buildPath);
        }
    }

    public function tearDown(): void
    {
        (new ShikiNpmFetcher())->removeShikiAndNodeModules();
        $buildPath = (string) Str::of(\Kickflip\KickflipHelper::namedPath(\Kickflip\Enums\CliStateDirPaths::BuildDestination))->replaceEnv(self::BUILD_ENV);
        if (is_dir($buildPath)) {
            File::deleteDirectory($buildPath);
        }
        parent::tearDown();
    }

    public function testBuildCommand()
    {
        $this->artisan('build')
            ->assertSuccessful();
    }

    public function testSuccessfulFakeDirtyBuild()
    {
        $buildPath = (string) Str::of(\Kickflip\KickflipHelper::namedPath(\Kickflip\Enums\CliStateDirPaths::BuildDestination))->replaceEnv(self::BUILD_ENV);
        mkdir($buildPath);

        $this->artisan('build')
            ->expectsConfirmation('Overwrite "' . $buildPath . '"? ', 'yes')
            ->assertSuccessful();
    }

    public function testDeniedFakeDirtyBuild()
    {
        $buildPath = (string) Str::of(\Kickflip\KickflipHelper::namedPath(\Kickflip\Enums\CliStateDirPaths::BuildDestination))->replaceEnv(self::BUILD_ENV);
        mkdir($buildPath);

        $this->artisan('build')
            ->expectsConfirmation('Overwrite "' . $buildPath . '"? ', 'no')
            ->assertFailed();
    }
}
