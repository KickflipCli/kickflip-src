<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\ShikiFetcher;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Kickflip\SiteBuilder\ShikiNpmFetcher;
use KickflipMonoTests\Feature\BaseFeatureTestCase;
use KickflipMonoTests\NpmHelpers;

use function file_get_contents;
use function file_put_contents;
use function filter_var;
use function str_replace;

use const FILTER_VALIDATE_INT;

class ShikiFetcherRemoveBaseFeatureTest extends BaseFeatureTestCase
{
    use NpmHelpers;

    public function setUp(): void
    {
        parent::setUp();
        (new ShikiNpmFetcher())->installShiki();
    }

    public function tearDown(): void
    {
        (new ShikiNpmFetcher())->removeShikiAndNodeModules();
        parent::tearDown();
    }

    public function testRemovingShikiAndNodeModules()
    {
        $shikiFetcher = new ShikiNpmFetcher();
        if (filter_var(Str::of($this->getNodeVersion())->before('.')->after('v'), FILTER_VALIDATE_INT) >= 15) {
            self::assertFileIsReadable($shikiFetcher->getProjectRootDirectory() . self::agnosticPath('/package.json'));
        }
        self::assertFileIsReadable($shikiFetcher->getProjectRootDirectory() . self::agnosticPath('/package-lock.json'));
        self::assertDirectoryIsWritable($shikiFetcher->getProjectRootDirectory() . self::agnosticPath('/node_modules'));

        $shikiFetcher->removeShikiAndNodeModules();

        self::assertFileDoesNotExist($shikiFetcher->getProjectRootDirectory() . self::agnosticPath('/package.json'));
        // phpcs:ignore
        self::assertFileDoesNotExist($shikiFetcher->getProjectRootDirectory() . self::agnosticPath('/package-lock.json'));
        // phpcs:ignore
        self::assertDirectoryDoesNotExist($shikiFetcher->getProjectRootDirectory() . self::agnosticPath('/node_modules'));
    }

    public function testCanFindShikiInDepsOrDevDeps()
    {
        $shikiFetcher = new ShikiNpmFetcher();
        $filePath = $shikiFetcher->getProjectRootDirectory() . self::agnosticPath('/package.json');
        if (!File::exists($filePath)) {
            $filePath = $shikiFetcher->getProjectRootDirectory() . self::agnosticPath('/package-lock.json');
        } else {
            File::delete($shikiFetcher->getProjectRootDirectory() . self::agnosticPath('/package-lock.json'));
        }
        // Initial test
        self::assertFileIsReadable($filePath);
        self::assertTrue($shikiFetcher->isShikiRequired());
        // Change devDeps to deps...
        file_put_contents($filePath, str_replace('devDependencies', 'dependencies', file_get_contents($filePath)));
        self::assertTrue($shikiFetcher->isShikiRequired());
        // Change deps to boogers...
        file_put_contents($filePath, str_replace('dependencies', 'boogers', file_get_contents($filePath)));
        self::assertFalse($shikiFetcher->isShikiRequired());
    }
}
