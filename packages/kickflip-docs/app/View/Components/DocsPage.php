<?php

namespace KickflipDocs\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\View\View;

use function view;

class DocsPage extends Component
{
    /**
     * @var array
     */
    public array $navigation;

    public string $content;

    /**
     * Create the component instance.
     *
     * @param array $navigation
     *
     * @return void
     */
    public function __construct(array $navigation)
    {
        $this->navigation = $navigation;
    }

    public function render(): string | Closure | View
    {
        return view('layouts.documentation');
    }
}
