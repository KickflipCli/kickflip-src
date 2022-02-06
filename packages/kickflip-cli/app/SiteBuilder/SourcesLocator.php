<?php

declare(strict_types=1);

namespace Kickflip\SiteBuilder;

use BadMethodCallException;
use Illuminate\Support\Str;
use Kickflip\KickflipHelper;
use Kickflip\Models\ContentFileData;
use Kickflip\Models\PageData;
use Kickflip\Models\SourcePageMetaData;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

use function array_flip;
use function array_map;
use function collect;
use function file_get_contents;
use function iterator_to_array;
use function strcmp;

final class SourcesLocator
{
    /**
     * @var array<int, PageData>
     */
    private array $renderPageList;

    /**
     * @var SourcePageMetaData[]
     */
    private array $bladeSources = [];

    /**
     * @var SourcePageMetaData[]
     */
    private array $markdownSources = [];

    /**
     * @var SourcePageMetaData[]
     */
    private array $markdownBladeSources = [];

    /**
     * @var ContentFileData[]
     */
    private array $plainTextOrMediaSources = [];

    public function __construct(
        private string $sourcesBasePath,
    ) {
        // Filter out anything in the assets' folder
        // These were compiled from mix into that folder before compiling the site.
        $allSourceFiles = collect(iterator_to_array(
            Finder::create()
                ->files()
                ->ignoreDotFiles(true)
                ->in($this->sourcesBasePath)
                ->sortByName(),
            false,
        ))->filter(static function (SplFileInfo $value) {
            $relativePath = Str::of($value->getRelativePath());

            return ! $relativePath->startsWith('assets') && ! $relativePath->startsWith('_');
        });
        // TODO: add a step that adds collection items into their respective collection...
        $sourcesCount = $allSourceFiles->count();
        for ($i = 0; $i < $sourcesCount; $i++) {
            /**
             * @var SplFileInfo $fileInfo
             */
            $fileInfo = $allSourceFiles->shift();
            $pageMetaData = SourcePageMetaData::fromSplFileInfo($fileInfo);
            match ($pageMetaData->getExtension()) {
                'blade.php' => $this->bladeSources[] = $pageMetaData,
                'md', 'markdown' => $this->markdownSources[] = $pageMetaData,
                'md.blade.php', 'blade.md', 'blade.markdown' => $this->markdownBladeSources[] = $pageMetaData,
                'html', 'txt', 'ico' => $this->plainTextOrMediaSources[] = ContentFileData::make($pageMetaData),
                default => 'do nothing',
            };
        }
        unset($allSourceFiles);
        $this->buildRenderList();
    }

    private function buildRenderList(): void
    {
        $renderList = collect($this->bladeSources)
            ->merge($this->markdownSources)
            ->merge($this->markdownBladeSources)
            ->sort(static fn ($fileOne, $fileTwo) => strcmp($fileOne->getName(), $fileTwo->getName()))
            ->sort(static fn ($fileOne, $fileTwo) => strcmp(
                $fileOne->getRelativeDirectoryPath(),
                $fileTwo->getRelativeDirectoryPath(),
            ))
            ->values();

        // Compile source pages into PageData objects
        $renderList->map(function (SourcePageMetaData $sourcePageMetaData) {
            $frontMatterData = KickflipHelper::getFrontMatterParser()
                    ->parse(file_get_contents($sourcePageMetaData->getFullPath()))
                    ->getFrontMatter() ?? [];
            $this->renderPageList[] = PageData::make(
                $sourcePageMetaData,
                $frontMatterData,
            );
        });
    }

    /**
     * @return array<int, PageData>
     */
    public function getRenderPageList(): array
    {
        // TODO: find a way to make this consider collection pages too
        return $this->renderPageList;
    }

    public function getRenderPageByName(string $name): PageData
    {
        $nameKeys = array_flip(array_map(
            fn (PageData $page) => $page->source->getName(),
            $this->renderPageList,
        ));

        if (!isset($nameKeys[$name])) {
            throw new BadMethodCallException("Cannot find source file by name: '$name'");
        }

        return $this->renderPageList[$nameKeys[$name]];
    }

    /**
     * @return ContentFileData[]
     */
    public function getCopyFileList(): array
    {
        return $this->plainTextOrMediaSources;
    }
}
