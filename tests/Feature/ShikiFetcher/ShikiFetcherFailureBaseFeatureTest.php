<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\ShikiFetcher;

use Illuminate\Support\Facades\File;
use Kickflip\SiteBuilder\ShikiNpmFetcher;
use KickflipMonoTests\Feature\BaseFeatureTestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;

use function touch;

use const DIRECTORY_SEPARATOR;

class ShikiFetcherFailureBaseFeatureTest extends BaseFeatureTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        (new ShikiNpmFetcher())->removeShikiAndNodeModules();
    }

    public function tearDown(): void
    {
        $shikiFetcher = new ShikiNpmFetcher();
        $nodeModules = $shikiFetcher->getProjectRootDirectory() . DIRECTORY_SEPARATOR . 'node_modules';
        $packageJson = $shikiFetcher->getProjectRootDirectory() . DIRECTORY_SEPARATOR . 'package.json';
        $packageLock = $shikiFetcher->getProjectRootDirectory() . DIRECTORY_SEPARATOR . 'package-lock.json';
        File::chmod($packageJson, 0777);
        File::chmod($packageLock, 0777);
        File::chmod($nodeModules, 0777);
        $shikiFetcher->removeShikiAndNodeModules();
        parent::tearDown();
    }

    public function testExpectsExceptionWhenShikiFetcherFails()
    {
        // Create shiki fetcher
        $shikiFetcher = new ShikiNpmFetcher();
        $nodeModules = $shikiFetcher->getProjectRootDirectory() . DIRECTORY_SEPARATOR . 'node_modules';
        $packageJson = $shikiFetcher->getProjectRootDirectory() . DIRECTORY_SEPARATOR . 'package.json';
        $packageLock = $shikiFetcher->getProjectRootDirectory() . DIRECTORY_SEPARATOR . 'package-lock.json';
        // Setup directory that will cause failure...
        touch($packageJson);
        touch($packageLock);
        File::ensureDirectoryExists($nodeModules, 0500);
        File::chmod($packageJson, 0400);
        File::chmod($packageLock, 0400);
        File::chmod($nodeModules, 0400);
        self::assertFileExists($packageJson);
        self::assertFileExists($packageLock);
        self::assertDirectoryExists($nodeModules);

        // Expect the exception and trigger the failure
        $this->expectException(ProcessFailedException::class);
        $shikiFetcher->installShiki();
    }
}
