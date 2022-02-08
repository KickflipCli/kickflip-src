<?php

declare(strict_types=1);

namespace Kickflip\Models;

use Exception;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;
use Kickflip\Collection\CollectionConfig;
use Kickflip\Collection\PageCollection;
use Kickflip\Events\PageDataCreated;
use Kickflip\KickflipHelper;
use RuntimeException;

use function array_key_exists;
use function debug_backtrace;
use function lcfirst;

use const DIRECTORY_SEPARATOR;

class PageData implements PageInterface
{
    public static string $defaultExtendsView = 'layouts.master';
    public static string $defaultExtendsSection = 'body';

    /**
     * @param array<string, mixed> $data
     */
    private function __construct(
        public SourcePageMetaData $source,
        public string $url,
        public string $title,
        public array $data = [],
        public string | null $description = null,
        public bool | null $autoExtend = true,
        public ExtendsInfo | null $extends = null,
        protected bool $isCollectionItem = false,
        protected string | null $collectionName = null,
        protected int | null $collectionIndex = null,
    ) {
    }

    protected static function determineMetaDataUrl(SourcePageMetaData $metaData): string
    {
        return $metaData->getName() === 'index' ? '/' :
            (string) Str::of($metaData->getName())
                        ->replace('.', '/')
                        ->prepend('/');
    }

    protected static function determineCollectionMetaDataUrl(
        CollectionConfig $itemCollectionConfig,
        SourcePageMetaData $metaData
    ): string {
        $defaultUrl = $metaData->getName() === 'index' ? '/' :
            (string) Str::of($metaData->getName())
                ->replace('.', '/')
                ->prepend('/');

        // TODO: make this more configurable...
        return (string) Str::of($defaultUrl)->replace($itemCollectionConfig->path, $itemCollectionConfig->url);
    }

    protected static function determineMetaDataTitle(SourcePageMetaData $metaData): string
    {
        $sourceString = Str::of($metaData->getName())->afterLast('.');

        return (string) $sourceString
                ->replace('-', ' ')
                ->replace('.', ' ')
                ->title();
    }

    /**
     * @param array<string, mixed> $frontMatter
     */
    public static function make(SourcePageMetaData $metaData, array $frontMatter = []): PageData
    {
        $url = $frontMatter['url'] ?? static::determineMetaDataUrl($metaData);

        $title = $frontMatter['title'] ?? static::determineMetaDataTitle($metaData);

        $frontMatterData = $frontMatter;
        unset(
            $frontMatterData['title'],
            $frontMatterData['description'],
            $frontMatterData['autoExtend'],
            $frontMatterData['extends'],
            $frontMatterData['section'],
        );

        $newPageData = new self(
            source: $metaData,
            url: $url,
            title: $title,
            data: $frontMatterData,
        );

        self::setOnInstanceFromFrontMatterIfNotNull($newPageData, $frontMatter, 'description');
        self::setOnInstanceFromFrontMatterIfNotNull($newPageData, $frontMatter, 'autoExtend');
        if ($newPageData->autoExtend) {
            $newPageData->extends = ExtendsInfo::make(
                $frontMatter['extends'] ?? self::$defaultExtendsView,
                $frontMatter['section'] ?? self::$defaultExtendsSection,
            );
        }
        PageDataCreated::dispatch($newPageData);

        return $newPageData;
    }

    /**
     * @param array<string, mixed> $frontMatter
     */
    public static function makeFromCollection(
        PageCollection $itemCollection,
        int $collectionIndex,
        SourcePageMetaData $metaData,
        array $frontMatter = []
    ): PageData {
        $url = $frontMatter['url'] ?? static::determineCollectionMetaDataUrl($itemCollection->config, $metaData);
        $title = $frontMatter['title'] ?? static::determineMetaDataTitle($metaData);

        $frontMatterData = $frontMatter;
        unset(
            $frontMatterData['title'],
            $frontMatterData['description'],
            $frontMatterData['autoExtend'],
            $frontMatterData['extends'],
            $frontMatterData['section'],
        );

        $newPageData = new self(
            source: $metaData,
            url: $url,
            title: $title,
            data: $frontMatterData,
            isCollectionItem: true,
            collectionName: $itemCollection->config->name,
            collectionIndex: $collectionIndex,
        );

        // TODO: modify for collections
        self::setOnInstanceFromFrontMatterIfNotNull($newPageData, $frontMatter, 'description');
        self::setOnInstanceFromFrontMatterIfNotNull($newPageData, $frontMatter, 'autoExtend');
        if ($newPageData->autoExtend) {
            $newPageData->extends = ExtendsInfo::make(
                $frontMatter['extends'] ?? $itemCollection->config->extends?->view ?? self::$defaultExtendsView,
                $frontMatter['section'] ?? $itemCollection->config->extends?->section ?? self::$defaultExtendsSection,
            );
        }
        PageDataCreated::dispatch($newPageData);

        return $newPageData;
    }

    /**
     * @param PageData $instance
     * @param array<string, mixed> $frontMatterData
     */
    private static function setOnInstanceFromFrontMatterIfNotNull(
        self $instance,
        array $frontMatterData,
        string $propertyName
    ): void {
        if (
            array_key_exists($propertyName, $frontMatterData) &&
            $frontMatterData[$propertyName] !== null
        ) {
            $instance->{$propertyName} = $frontMatterData[$propertyName];
        }
    }

    public function isCollectionItem(): bool
    {
        return $this->isCollectionItem;
    }

    public function getCollectionName(): string
    {
        if (!$this->isCollectionItem || $this->collectionName === null) {
            throw new RuntimeException('Should only call `getCollectionName` on PageData part of a collection');
        }

        return $this->collectionName;
    }

    public function getCollectionIndex(): int
    {
        if (!$this->isCollectionItem) {
            throw new RuntimeException('Should only call `getCollectionIndex` on PageData part of a collection');
        }

        return $this->collectionIndex + 1;
    }

    public function updateCollectionIndex(int $index)
    {
        if (!$this->isCollectionItem) {
            throw new RuntimeException('Cannot set collection index on page not in a collection');
        }
        $this->collectionIndex = $index;
    }

    public function getCollection(): PageCollection
    {
        if (!$this->isCollectionItem) {
            throw new RuntimeException('Should only call `getCollection` on PageData part of a collection');
        }
        /**
         * @var PageCollection[] $collections
         */
        $collections = KickflipHelper::config('collections');

        return $collections[$this->collectionName];
    }

    public function getPreviousNextPaginator(): Paginator
    {
        if (!$this->isCollectionItem) {
            throw new RuntimeException('Should only call `getPreviousNextPaginator` on PageData part of a collection');
        }

        $pageCollection = $this->getCollection();

        return $pageCollection->backAndNextPaginate($this);
    }

    public function getPaginator(): ?Paginator
    {
        if (!$this->isCollectionItem) {
            return null;
        }

        $pageCollection = $this->getCollection();

        return $pageCollection->paginate($this);
    }

    public function getUrl(): string
    {
        if ($this->url === '/') {
            return $this->url;
        }

        if (KickflipHelper::config('prettyUrls', true) === true) {
            return $this->url;
        }

        return "{$this->url}.html";
    }

    public function getOutputPath(): string
    {
        if ($this->url === '/') {
            return KickflipHelper::buildPath('index.html');
        }
        $url = $this->getUrl();
        if (KickflipHelper::config('prettyUrls', true) === true) {
            $url .= DIRECTORY_SEPARATOR . 'index.html';
        }

        return KickflipHelper::buildPath($url);
    }

    public function getExtendsView(): string | null
    {
        return $this->extends?->view;
    }

    public function getExtendsSection(): string | null
    {
        return $this->extends?->section;
    }

    public function getTitleId(): string
    {
        return KickflipHelper::toKebab($this->title);
    }

    /**
     * @return array<string, mixed>
     */
    public function getExtraData(): array
    {
        return $this->data;
    }

    /**
     * @return mixed
     *
     * @throws Exception
     */
    public function __get(string $name)
    {
        if (
            array_key_exists($name, $this->data) ||
            Str::of($name)->startsWith('get') && $name = lcfirst((string) Str::of($name)->replaceFirst('get', ''))
        ) {
            return $this->data[$name];
        }

        $trace = debug_backtrace();

        throw new Exception(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
        );
    }
}
