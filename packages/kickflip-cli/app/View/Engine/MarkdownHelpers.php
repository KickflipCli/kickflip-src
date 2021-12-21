<?php

declare(strict_types=1);

namespace Kickflip\View\Engine;

use Illuminate\View\Factory;
use Kickflip\Models\PageData;
use Kickflip\Models\SiteData;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Output\RenderedContentInterface;

trait MarkdownHelpers
{
    public function isAutoExtendEnabled(SiteData $siteData, PageData $pageData): bool
    {
        return $siteData->autoExtendMarkdown === true && $pageData->autoExtend === true;
    }

    public function isPageExtendEnabled(PageData $pageData, $renderedMarkdown): bool
    {
        return $renderedMarkdown instanceof RenderedContentWithFrontMatter &&
                    $pageData->autoExtend === true;
    }

    public function prepareExtendedRender(RenderedContentInterface $renderedMarkdown): array
    {
        // Prepare view data based on which instance it is
        if ($renderedMarkdown instanceof RenderedContentWithFrontMatter) {
            $frontMatter = $renderedMarkdown->getFrontMatter();
            $section = $frontMatter['section'] ?? PageData::$defaultExtendsSection;
            $content = (string) $renderedMarkdown->getContent();
            $extends = $frontMatter['extends'] ?? PageData::$defaultExtendsView;
        } else {
            $section = PageData::$defaultExtendsSection;
            $content = (string) $renderedMarkdown;
            $extends = PageData::$defaultExtendsView;
        }

        return [
            $section,
            $content,
            $extends,
        ];
    }

    public function makeView(array $data, RenderedContentInterface $renderedMarkdown)
    {
        /**
         * @var Factory $viewFactory
         */
        $viewFactory = $data['__env'];

        // Prepare view data based on which instance it is
        [ $section, $content, $extends ] = $this->prepareExtendedRender($renderedMarkdown);

        // "Push" the section content in a way that respects the rendered HTML from markdown
        $viewFactory->startSection($section);
        echo $content;
        $viewFactory->stopSection();

        return $viewFactory->make($extends, $data);
    }
}
