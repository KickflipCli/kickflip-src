<?php

declare(strict_types=1);

namespace Kickflip\Providers;

use Illuminate\View\DynamicComponent;
use Illuminate\View\ViewServiceProvider as BaseViewServiceProvider;
use Kickflip\View\Compilers\BladeCompiler;

use function tap;

class ViewServiceProvider extends BaseViewServiceProvider
{
    /**
     * Register the Blade compiler implementation.
     *
     * @return void
     */
    public function registerBladeCompiler()
    {
        $this->app->singleton(
            'blade.compiler',
            fn ($app) => tap(new BladeCompiler($app['files'], $app['config']['view.compiled']), function ($blade) {
                $blade->component('dynamic-component', DynamicComponent::class);
            }),
        );
    }
}
