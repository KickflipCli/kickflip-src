<?php

declare(strict_types=1);

namespace Kickflip\SiteBuilder;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View as ViewContract;
use Kickflip\KickflipHelper;
use Navindex\HtmlFormatter\Formatter;
use Throwable;

final class HtmlFormatter
{
    public static function render(Factory | ViewContract $view): string
    {
        $renderedHtml = $view->render();
        $formatter = new Formatter();

        try {
            if (KickflipHelper::config()->get('minify_html', false)) {
                return @$formatter->minify($renderedHtml);
            }

            return @$formatter->beautify($renderedHtml);
        } catch (Throwable) {
            return $renderedHtml;
        }
    }
}
