<?php

declare(strict_types=1);

namespace Kickflip\Models;

use JetBrains\PhpStorm\Pure;

class ExtendsInfo
{
    private function __construct(
        public string $view,
        public string $section,
    ) {
    }

    #[Pure]
    public static function make(string $view = 'layouts.master', string $section = 'body'): ExtendsInfo
    {
        return new self($view, $section);
    }
}
