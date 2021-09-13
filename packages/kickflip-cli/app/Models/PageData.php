<?php

namespace Kickflip\Models;

class PageData
{
    public function __construct(
        public string $url,
        public string $title,
        public ?string $description = null,
        public ?string $extends = 'layouts.master',
        public ?string $section = 'content',
        public ?array $data = [],
    ) {
    }

    public static function get404(): self
    {
        return new self(url: '404', title: '404 Not Found', description: '404 URL Not Found');
    }

    /**
     * @param array{title: string, description: string} $frontMatter
     * @return PageData
     */
    public static function fromFrontMatter(array $frontMatter)
    {
        $frontMatterData = $frontMatter;
        unset(
            $frontMatterData['title'],
            $frontMatterData['description'],
            $frontMatterData['extends'],
            $frontMatterData['section'],
        );
        return new self(
            url: 'YEET',
            title: $frontMatter['title'],
            description: $frontMatter['description'],
            extends: $frontMatter['extends'] ?? null,
            section: $frontMatter['section'] ?? null,
            data: $frontMatterData ?? null,
        );
    }

    public function getUrl()
    {
        return relativeUrl($this->url);
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }
}
