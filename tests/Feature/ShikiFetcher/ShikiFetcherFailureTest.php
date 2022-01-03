<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\ShikiFetcher;

use Illuminate\Support\Facades\File;
use Kickflip\SiteBuilder\ShikiNpmFetcher;
use KickflipMonoTests\TestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;

use function chmod;
use function mkdir;
use function touch;

class ShikiFetcherFailureTest extends TestCase
{
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

    public function testExpectsExceptionWhenShikiFetcherFails()
    {
        // Create shiki fetcher
        $shikiFetcher = new ShikiNpmFetcher();
        $nodeModules = $shikiFetcher->getProjectRootDirectory() . '/node_modules';
        $packageJson = $shikiFetcher->getProjectRootDirectory() . '/package.json';
        $packageLock = $shikiFetcher->getProjectRootDirectory() . '/package-lock.json';
        // Setup directory that will cause failure...
        mkdir($nodeModules, 0500);
        touch($packageJson);
        touch($packageLock);
        chmod($packageJson, 0400);
        chmod($packageLock, 0400);
        chmod($nodeModules, 0400);
        self::assertDirectoryExists($nodeModules);
        // Expect the exception and trigger the failure
        $this->expectException(ProcessFailedException::class);
        $shikiFetcher->installShiki();
        // Ensure 0500 perms directory is removed
        chmod($packageJson, 0700);
        chmod($packageLock, 0700);
        chmod($nodeModules, 0700);
        File::delete($packageLock);
        File::deleteDirectory($nodeModules);
        self::assertDirectoryDoesNotExist($nodeModules);
    }
}
