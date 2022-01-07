<?php

declare(strict_types=1);

namespace KickflipMonoTests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\File;
use Kickflip\KickflipHelper;
use Kickflip\Models\PageData;
use Kickflip\Models\SourcePageMetaData;
use LaravelZero\Framework\Testing\TestCase as BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;

use function collect;
use function file_get_contents;

abstract class DocsTestCase extends BaseTestCase
{
    use PlatformAgnosticHelpers;
    use MatchesSnapshots;
    use DataProviderHelpers;

    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        /**
         * @var \LaravelZero\Framework\Application $app
         */
        $app = require __DIR__ . '/../packages/kickflip-cli/bootstrap/app.php';
        KickflipHelper::setPaths(KickflipHelper::basePath(__DIR__ . self::agnosticPath('/../packages/kickflip-docs')));
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    public function getDocsPageData(string $pageName): PageData
    {
        $allSources = collect(File::allfiles(KickflipHelper::sourcePath()))
            ->map(fn ($splFileInfo) => SourcePageMetaData::fromSplFileInfo($splFileInfo))
            ->filter(fn (SourcePageMetaData $sourcePageMetaData) => match ($sourcePageMetaData->getExtension()) {
                    'blade.php', 'md', 'markdown',
                    'md.blade.php', 'blade.md', 'blade.markdown' => true,
                    default => false,
            })->mapWithKeys(fn (SourcePageMetaData $value, $key) => [$value->getName() => $value])->toArray();
        // Create a SourcePageMetaData object
        $sourcePageMetaData = $allSources[$pageName];
        // Parse out the front matter page metadata
        $frontMatterData = KickflipHelper::getFrontMatterParser()
                ->parse(file_get_contents($sourcePageMetaData->getFullPath()))
                ->getFrontMatter() ?? [];

        // Create a PageData object
        return PageData::make($sourcePageMetaData, $frontMatterData);
    }
}
