<?php

declare(strict_types=1);

namespace Kickflip\View\Compilers;

use Illuminate\View\Compilers\ComponentTagCompiler as BaseComponentTagCompiler;

class ComponentTagCompiler extends BaseComponentTagCompiler
{
    public static string $rootNamespace = 'App';

    /**
     * Guess the class name for the given component.
     *
     * @return string
     */
    public function guessClassName(string $component)
    {
        $class = $this->formatClassName($component);

        return static::$rootNamespace . '\\View\\Components\\' . $class;
    }
}
