<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\Models;

use Illuminate\Support\Facades\File;
use Kickflip\KickflipHelper;
use Kickflip\Models\PageData;
use Kickflip\Models\SourcePageMetaData;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\ReflectionHelpers;
use KickflipMonoTests\TestCase;

use function file_get_contents;

class SourcePageMetaDataTest extends TestCase
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
            'source/404.blade.php',
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
            'source/index.md',
            $pageData->source->getRelativePath(),
        );
    }
}
