<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\Models;

use Illuminate\Support\Facades\File;
use Kickflip\KickflipHelper;
use Kickflip\Models\PageData;
use Kickflip\Models\SourcePageMetaData;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\Feature\BaseFeatureTestCase;
use KickflipMonoTests\ReflectionHelpers;
use function file_get_contents;

class SourcePageMetaDataBaseFeatureTest extends BaseFeatureTestCase
{
    use DataProviderHelpers;
    use ReflectionHelpers;

    public function testCanInstantiateWithSourcePageMeta()
    {
        // Fetch a single Symfony SplFileInfo object
        $splFileInfo = File::files(KickflipHelper::sourcePath())[0];
        // Create a SourcePageMetaData object
        $sourcePageMetaData = SourcePageMetaData::fromSplFileInfo($splFileInfo);
        // Parse out the front matter page metadata
        $frontMatterData = KickflipHelper::getFrontMatterParser()
                ->parse(file_get_contents($sourcePageMetaData->getFullPath()))
                ->getFrontMatter() ?? [];

        // Create a PageData object
        $pageData = PageData::make($sourcePageMetaData, $frontMatterData);
        self::assertInstanceOf(PageData::class, $pageData);
        self::assertIsString($pageData->source->getRelativePath());
        self::assertEquals(
            self::agnosticPath('source/404.blade.php'),
            $pageData->source->getRelativePath(),
        );

        // Fetch a single Symfony SplFileInfo object
        $splFileInfo = File::files(KickflipHelper::sourcePath())[2];
        // Create a SourcePageMetaData object
        $sourcePageMetaData = SourcePageMetaData::fromSplFileInfo($splFileInfo);
        // Parse out the front matter page metadata
        $frontMatterData = KickflipHelper::getFrontMatterParser()
                ->parse(file_get_contents($sourcePageMetaData->getFullPath()))
                ->getFrontMatter() ?? [];

        // Create a PageData object
        $pageData = PageData::make($sourcePageMetaData, $frontMatterData);
        self::assertInstanceOf(PageData::class, $pageData);
        self::assertIsString($pageData->source->getRelativePath());
        self::assertEquals(
            self::agnosticPath('source/index.md'),
            $pageData->source->getRelativePath(),
        );
    }
}
