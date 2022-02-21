<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\NpmFetcher;

use Illuminate\Support\Facades\File;
use Kickflip\SiteBuilder\NpmFetcher;
use KickflipMonoTests\Feature\BaseFeatureTestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;

use function touch;

use const DIRECTORY_SEPARATOR;

class NpmFetcherFailureBaseFeatureTest extends BaseFeatureTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        (new NpmFetcher())->removeAndCleanNodeModules();
    }

    public function tearDown(): void
    {
        $npmFetcher = new NpmFetcher();
        $nodeModules = $npmFetcher->getProjectRootDirectory() . DIRECTORY_SEPARATOR . 'node_modules';
        $packageJson = $npmFetcher->getProjectRootDirectory() . DIRECTORY_SEPARATOR . 'package.json';
        $packageLock = $npmFetcher->getProjectRootDirectory() . DIRECTORY_SEPARATOR . 'package-lock.json';
        File::chmod($packageJson, 0777);
        File::chmod($packageLock, 0777);
        File::chmod($nodeModules, 0777);
        $npmFetcher->removeAndCleanNodeModules();
        parent::tearDown();
    }

    public function testExpectsExceptionWhenShikiFetcherFails()
    {
        // Create NPM fetcher
        $npmFetcher = new NpmFetcher();
        $nodeModules = $npmFetcher->getProjectRootDirectory() . DIRECTORY_SEPARATOR . 'node_modules';
        $packageJson = $npmFetcher->getProjectRootDirectory() . DIRECTORY_SEPARATOR . 'package.json';
        $packageLock = $npmFetcher->getProjectRootDirectory() . DIRECTORY_SEPARATOR . 'package-lock.json';
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
        $npmFetcher->installPackage('shiki');
    }
}
