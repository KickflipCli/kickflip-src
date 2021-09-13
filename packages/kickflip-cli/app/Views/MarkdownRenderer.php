<?php

namespace Kickflip\Views;

use League\CommonMark\Environment\Environment;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Output\RenderedContentInterface;
use Spatie\LaravelMarkdown\MarkdownRenderer as BaseMarkdownRenderer;

class MarkdownRenderer extends BaseMarkdownRenderer
{
    private function getMarkdownConverter(): MarkdownConverter
    {
        $environment = new Environment($this->commonmarkOptions);
        $this->configureCommonMarkEnvironment($environment);

        return new MarkdownConverter(
            environment: $environment
        );
    }

    protected function convertMarkdownToHtml(string $markdown): string
    {
        $commonMarkConverter = $this->getMarkdownConverter();

        return $commonMarkConverter->convertToHtml($markdown);
    }

    public function convertToHtml(string $markdown): RenderedContentInterface
    {
        return $this->getMarkdownConverter()->convertToHtml($markdown);
    }
}
