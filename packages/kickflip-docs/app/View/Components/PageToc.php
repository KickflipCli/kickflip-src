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
use function count;
use function sprintf;

use const PHP_EOL;

class PageToc extends Component
{
    private HtmlRenderer $htmlRenderer;
    private string $headingElement = '<h2 class="text-lg font-thin">%s</h2>';

    public function __construct(
        private ?string $heading
    ) {
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
        if ($tableOfContents === null || count($tableOfContents->children()) === 0) {
            return '';
        }
        $tableOfContentsHtml = sprintf($this->headingElement, $this->heading) . PHP_EOL;
        $tableOfContentsHtml .= new HtmlString($this->htmlRenderer->renderNodes([$tableOfContents]));
        $kickflipConfig->set('pageToc', null);

        return $tableOfContentsHtml;
    }
}
