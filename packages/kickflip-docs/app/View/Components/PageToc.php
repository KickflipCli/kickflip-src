<?php

declare(strict_types=1);

namespace KickflipDocs\View\Components;

use Illuminate\Support\HtmlString;
use Illuminate\View\Component;
use Kickflip\KickflipHelper;
use Kickflip\View\MarkdownRenderer;
use League\CommonMark\Extension\TableOfContents\Node\TableOfContents;
use League\CommonMark\Renderer\HtmlRenderer;

use function app;

class PageToc extends Component
{
    private HtmlRenderer $htmlRenderer;

    public function __construct()
    {
        $this->htmlRenderer = new HtmlRenderer(app(MarkdownRenderer::class)->getMarkdownEnvironment());
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $kickflipConfig = KickflipHelper::getKickflipState();
        /**
         * @var TableOfContents $tableOfContents
         */
        $tableOfContents = $kickflipConfig->get('pageToc', null);
        if ($tableOfContents === null) {
            return '';
        }
        $tableOfContentsHtml = new HtmlString($this->htmlRenderer->renderNodes([$tableOfContents]));
        $kickflipConfig->set('pageToc', null);

        return $tableOfContentsHtml;
    }
}
