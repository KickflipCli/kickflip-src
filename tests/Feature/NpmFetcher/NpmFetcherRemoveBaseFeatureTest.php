<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\NpmFetcher;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Kickflip\SiteBuilder\NpmFetcher;
use KickflipMonoTests\Feature\BaseFeatureTestCase;
use KickflipMonoTests\NpmHelpers;

use function file_get_contents;
use function file_put_contents;
use function filter_var;
use function str_replace;

use const FILTER_VALIDATE_INT;

class NpmFetcherRemoveBaseFeatureTest extends BaseFeatureTestCase
{
    use NpmHelpers;

    public function setUp(): void
    {
        parent::setUp();
        (new NpmFetcher())->installPackage('shiki');
    }

    public function tearDown(): void
    {
        (new NpmFetcher())->removeAndCleanNodeModules();
        parent::tearDown();
    }

    public function testRemovingShikiAndNodeModules()
    {
        $npmFetcher = new NpmFetcher();
        if (filter_var(Str::of($this->getNodeVersion())->before('.')->after('v'), FILTER_VALIDATE_INT) >= 15) {
            self::assertFileIsReadable($npmFetcher->getProjectRootDirectory() . self::agnosticPath('/package.json'));
        }
        self::assertFileIsReadable($npmFetcher->getProjectRootDirectory() . self::agnosticPath('/package-lock.json'));
        self::assertDirectoryIsWritable($npmFetcher->getProjectRootDirectory() . self::agnosticPath('/node_modules'));

        $npmFetcher->removeAndCleanNodeModules();

        self::assertFileDoesNotExist($npmFetcher->getProjectRootDirectory() . self::agnosticPath('/package.json'));
        // phpcs:ignore
        self::assertFileDoesNotExist($npmFetcher->getProjectRootDirectory() . self::agnosticPath('/package-lock.json'));
        // phpcs:ignore
        self::assertDirectoryDoesNotExist($npmFetcher->getProjectRootDirectory() . self::agnosticPath('/node_modules'));
    }

    public function testCanFindShikiInDepsOrDevDeps()
    {
        $npmFetcher = new NpmFetcher();
        $filePath = $npmFetcher->getProjectRootDirectory() . self::agnosticPath('/package.json');
        if (!File::exists($filePath)) {
            $filePath = $npmFetcher->getProjectRootDirectory() . self::agnosticPath('/package-lock.json');
        } else {
            File::delete($npmFetcher->getProjectRootDirectory() . self::agnosticPath('/package-lock.json'));
        }
        // Initial test
        self::assertFileIsReadable($filePath);
        self::assertTrue($npmFetcher->isRequired());
        // Change devDeps to deps...
        file_put_contents($filePath, str_replace('devDependencies', 'dependencies', file_get_contents($filePath)));
        self::assertTrue($npmFetcher->isRequired());
        // Change deps to boogers...
        file_put_contents($filePath, str_replace('dependencies', 'boogers', file_get_contents($filePath)));
        self::assertFalse($npmFetcher->isRequired());
    }
}
