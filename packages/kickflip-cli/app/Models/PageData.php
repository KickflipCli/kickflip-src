<?php

declare(strict_types=1);

namespace Kickflip\Models;

use Illuminate\Support\Str;
use Kickflip\Events\PageDataCreated;
use Kickflip\KickflipHelper;

/**
 * @property string $sourceFile The path to the source file for this page.
 */
class PageData implements PageInterface
{
    public static string $defaultExtendsView = 'layouts.master';
    public static string $defaultExtendsSection = 'body';

    private function __construct(
        public SourcePageMetaData $source,
        public string $url,
        public string $title,
        public ?string $description = null,
        public bool $autoExtend = true,
        public ?string $extends = null,
        public ?string $section = null,
        /**
         * @var array<string, mixed>|null
         */
        public ?array $data = [],
    ) {
    }

    private static function determineMetaDataUrl(SourcePageMetaData $metaData): string
    {
        return ('index' === $metaData->getName()) ? '/' :
            (string) Str::of($metaData->getName())
                        ->replace('.', '/')
                        ->prepend('/');
    }

    private static function determineMetaDataTitle(SourcePageMetaData $metaData): string
    {
        $sourceString = Str::of($metaData->getName())->afterLast('.');
        return (string) $sourceString
                ->replace('-', ' ')
                ->replace('.', ' ')
                ->title();
    }

    /**
     * @param SourcePageMetaData $metaData
     * @param array<string, mixed> $frontMatter
     *
     * @return self
     */
    public static function make(SourcePageMetaData $metaData, array $frontMatter = []): self
    {
        $url = $frontMatter['url'] ?? static::determineMetaDataUrl($metaData);

        $title = $frontMatter['title'] ?? static::determineMetaDataTitle($metaData);

        /**
         * @var array<string, mixed> $frontMatterData
         */
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

        self::setOnInstanceFromFrontMatterIfNotNull($newPageData, $frontMatter,'description');
        self::setOnInstanceFromFrontMatterIfNotNull($newPageData, $frontMatter,'autoExtend');
        if ($newPageData->autoExtend) {
            $newPageData->extends = $frontMatter['extends'] ?? self::$defaultExtendsView;
            $newPageData->section = $frontMatter['section'] ?? self::$defaultExtendsSection;
        }
        PageDataCreated::dispatch($newPageData);
        return $newPageData;
    }

    /**
     * @param PageData $instance
     * @param array<string, mixed> $frontMatterData
     * @param string $propertyName
     */
    private static function setOnInstanceFromFrontMatterIfNotNull(self $instance, array $frontMatterData, string $propertyName): void
    {
        if (
            isset($frontMatterData[$propertyName]) &&
            $frontMatterData[$propertyName] !== null
        ) {
            $instance->{$propertyName} = $frontMatterData[$propertyName];
        }
    }

    public function getUrl(): string
    {
        $relUrl = KickflipHelper::relativeUrl($this->url);
        if ($this->url === '/' || KickflipHelper::config('prettyUrls', true) === true) {
            return $relUrl;
        }
        return (string) Str::of($relUrl)->append('.html');
    }

    public function getOutputPath(): string
    {
        if ($this->url === '/') {
            return KickflipHelper::buildPath('index.html');
        }
        $url = $this->getUrl();
        if (KickflipHelper::config('prettyUrls') === true) {
            $url .= '/index.html';
        }
        return KickflipHelper::buildPath($url);
    }

    public function getExtendsView(): string
    {
        return $this->extends;
    }

    public function getExtendsSection(): string
    {
        return $this->section;
    }

    public function getTitleId(): string
    {
        return KickflipHelper::toKebab($this->title);
    }

    public function getExtraData(): array
    {
        return $this->data;
    }

    /**
     * @param string $name
     * @return mixed
     *
     * @throws \Exception
     */
    public function __get(string $name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        $trace = debug_backtrace();
        throw new \Exception(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line']);
    }
}
