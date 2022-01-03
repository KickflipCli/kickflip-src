<?php

namespace KickflipDocs\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\View\View;

class DocsPage extends Component
{
    /**
     * @var array
     */
    public $navigation;

    /**
     * @var string
     */
    public $content;

    /**
     * Create the component instance.
     *
     * @param array $navigation
     * @return void
     */
    public function __construct(array $navigation)
    {
        $this->navigation = $navigation;
    }

    /**
     * @return View|Closure|string
     */
    public function render(): string|Closure|View
    {
        return view('layouts.documentation');
    }
}
