<?php

namespace KickflipDocs\View\Components;

use Illuminate\View\Component;

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
     * @inheritDoc
     */
    public function render()
    {
        return view('layouts.documentation');
    }
}
