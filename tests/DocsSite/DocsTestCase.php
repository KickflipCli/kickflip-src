<?php

declare(strict_types=1);

namespace KickflipMonoTests\DocsSite;

use Kickflip\KickflipHelper;
use Kickflip\Models\PageData;
use Kickflip\Models\SourcePageMetaData;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\Feature\BaseFeatureTestCase;

use function file_get_contents;
use function realpath;

abstract class DocsTestCase extends BaseFeatureTestCase
{
    use DataProviderHelpers;

    public bool $shouldRunShikiFetcher = false;

    public function basePath(): string
    {
        return realpath(__DIR__ . self::agnosticPath('/../../packages/kickflip-docs'));
    }

    public function getDocsPageData(string $pageName): PageData
    {
        $allSources = KickflipHelper::getFiles(KickflipHelper::sourcePath())
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
