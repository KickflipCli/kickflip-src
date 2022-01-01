<?php

declare(strict_types=1);

namespace Kickflip\View\Engine;

use Illuminate\Contracts\View\View;
use Illuminate\View\Factory;
use Kickflip\Models\PageData;
use Kickflip\Models\SiteData;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use League\CommonMark\Output\RenderedContentInterface;

trait MarkdownHelpers
{
    public function isAutoExtendEnabled(SiteData $siteData, PageData $pageData): bool
    {
        return $siteData->autoExtendMarkdown === true && $pageData->autoExtend !== false;
    }

    public function isPageExtendEnabled(PageData $pageData, $renderedMarkdown): bool
    {
        return $renderedMarkdown instanceof RenderedContentWithFrontMatter &&
                    $pageData->autoExtend === true;
    }

    /**
     * @param PageData                 $pageData
     * @param RenderedContentInterface $renderedMarkdown
     *
     * @return array
     */
    public function prepareExtendedRender(PageData $pageData, RenderedContentInterface $renderedMarkdown): array
    {
        // Prepare view data based on which instance it is
        if ($renderedMarkdown instanceof RenderedContentWithFrontMatter) {
            $content = $renderedMarkdown->getContent();
        } else {
            $content = (string) $renderedMarkdown;
        }

        return [
            $pageData->getExtendsSection(),
            $content,
            $pageData->getExtendsView(),
        ];
    }

    public function makeView(array $data, RenderedContentInterface $renderedMarkdown): View
    {
        /**
         * @var Factory $viewFactory
         */
        $viewFactory = $data['__env'];

        /**
         * @var string $section
         * @var string $content
         * @var string $extends
         */
        [ $section, $content, $extends ] = $this->prepareExtendedRender($data['page'], $renderedMarkdown);

        // "Push" the section content in a way that respects the rendered HTML from markdown
        $viewFactory->startSection($section);
        echo $content;
        $viewFactory->stopSection();

        return $viewFactory->make($extends, $data);
    }
}
