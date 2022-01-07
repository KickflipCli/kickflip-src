<?php

declare(strict_types=1);

namespace Kickflip\SiteBuilder;

use BadMethodCallException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Kickflip\KickflipHelper;
use Kickflip\Models\ContentFileData;
use Kickflip\Models\PageData;
use Kickflip\Models\SourcePageMetaData;
use Symfony\Component\Finder\SplFileInfo;

use function collect;
use function file_get_contents;

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
        $allSourceFiles = collect(File::allfiles($this->sourcesBasePath))
            ->filter(static fn ($value) => ! Str::of($value->getRelativePath())->startsWith('assets'));
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
        /**
         * @var SourcePageMetaData $bladeSource
         */
        foreach ($this->bladeSources as $bladeSource) {
            $frontMatterData = KickflipHelper::getFrontMatterParser()
                    ->parse(file_get_contents($bladeSource->getFullPath()))
                    ->getFrontMatter() ?? [];
            $this->renderPageList[] = PageData::make(
                $bladeSource,
                $frontMatterData,
            );
        }
        unset($bladeSource, $frontMatterData);

        /**
         * @var SourcePageMetaData $markdownSource
         */
        foreach ($this->markdownSources as $markdownSource) {
            $frontMatterData = KickflipHelper::getFrontMatterParser()
                    ->parse(file_get_contents($markdownSource->getFullPath()))
                    ->getFrontMatter() ?? [];
            $this->renderPageList[] = PageData::make(
                $markdownSource,
                $frontMatterData,
            );
        }
        unset($markdownSource, $frontMatterData);

        /**
         * @var SourcePageMetaData $markdownBladeSource
         */
        foreach ($this->markdownBladeSources as $markdownBladeSource) {
            $frontMatterData = KickflipHelper::getFrontMatterParser()
                    ->parse(file_get_contents($markdownBladeSource->getFullPath()))
                    ->getFrontMatter() ?? [];
            $this->renderPageList[] = PageData::make(
                $markdownBladeSource,
                $frontMatterData,
            );
        }
        unset($markdownBladeSource, $frontMatterData);
    }

    /**
     * @return array<int, PageData>
     */
    public function getRenderPageList(): array
    {
        return $this->renderPageList;
    }

    public function getRenderPageByName(string $name): PageData
    {
        $nameKeys = array_flip(array_map(
            fn(PageData $page) => $page->source->getName(),
            $this->renderPageList
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
