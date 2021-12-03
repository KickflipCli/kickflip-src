<?php

declare(strict_types=1);

namespace Kickflip\View\Engine;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\CompilerInterface;
use Illuminate\View\Engines\CompilerEngine;
use Spatie\LaravelMarkdown\MarkdownRenderer as BaseMarkdownRenderer;

class BladeMarkdownEngine extends CompilerEngine
{
    private BaseMarkdownRenderer $markdown;

    use MarkdownHelpers;

    public function __construct(CompilerInterface $compilerEngine, Filesystem $files, BaseMarkdownRenderer $markdownRenderer)
    {
        $this->markdown = $markdownRenderer;

        parent::__construct($compilerEngine, $files);
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @param string $path
     * @param array<string, mixed>  $data
     *
     * @return string
     */
    public function get($path, array $data = [])
    {
        // First gets the file contents, then renders blade parts into string before returning
        $contents = parent::get($path, $data);
        $renderedMarkdown = $this->markdown->convertToHtml($contents);

        /*
         * The control path here looks complex but is actually rather simple:
         * IF TRUE, we wrap the results in another view; or IF NOT TRUE, we return rendered markdown directly.
         *
         * The ways we know to wrap the markdown in another view is if:
         * 1) all non-FrontMatter markdown when autoExtendMarkdown set TRUE,
         * 2) any FrontMatter markdown without `autoExpand: false` passed,
         */
        if (
            $this->autoExtendEnabled($data['site'], $data['page']) ||
            $this->pageExtendEnabled($data['page'], $renderedMarkdown)
        ) {
            return $this->makeView($data, $renderedMarkdown)->render();
        }

        return (string) $renderedMarkdown;
    }
}
