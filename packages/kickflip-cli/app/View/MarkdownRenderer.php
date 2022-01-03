<?php

declare(strict_types=1);

namespace Kickflip\View;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\CommonMark\Node\Block\Heading;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use Spatie\CommonMarkShikiHighlighter\HighlightCodeExtension;
use Spatie\LaravelMarkdown\MarkdownRenderer as BaseMarkdownRenderer;
use Spatie\LaravelMarkdown\Renderers\AnchorHeadingRenderer;

use function class_exists;
use function is_string;

final class MarkdownRenderer extends BaseMarkdownRenderer
{
    protected function configureCommonMarkEnvironment(EnvironmentBuilderInterface $environment): void
    {
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new FrontMatterExtension());
        if ($this->highlightCode) {
            $environment->addExtension(new HighlightCodeExtension($this->highlightTheme));
        }

        if ($this->renderAnchors) {
            $environment->addRenderer(Heading::class, new AnchorHeadingRenderer());
        }

        foreach ($this->extensions as $extension) {
            if (is_string($extension) && class_exists($extension)) {
                $environment->addExtension(new $extension());
            }
        }

        foreach ($this->blockRenderers as $blockRenderer) {
            $environment->addRenderer(
                $blockRenderer['class'],
                $blockRenderer['renderer'],
                $blockRenderer['priority'] ?? 0,
            );
        }

        foreach ($this->inlineRenderers as $inlineRenderer) {
            $environment->addRenderer(
                $inlineRenderer['class'],
                $inlineRenderer['renderer'],
                $inlineRenderer['priority'] ?? 0,
            );
        }
    }

    public function getMarkdownEnvironment(): Environment
    {
        $environment = new Environment($this->commonmarkOptions);
        $this->configureCommonMarkEnvironment($environment);

        return $environment;
    }
}
