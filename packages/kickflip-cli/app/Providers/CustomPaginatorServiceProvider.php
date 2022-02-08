<?php

declare(strict_types=1);

namespace Kickflip\Providers;

use Illuminate\Pagination\PaginationServiceProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Kickflip\KickflipHelper;
use Kickflip\View\KickflipPaginator;
use ReflectionClass;

use function app;
use function dirname;
use function realpath;

class CustomPaginatorServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $langPath = app('path.lang');
        $this->registerTranslationLoader($langPath);

        KickflipPaginator::viewFactoryResolver(fn () => $this->app->get('view'));

        KickflipPaginator::currentPathResolver(fn () => $this->app->get('kickflipCli')->get('page')->getUrl());

        KickflipPaginator::queryStringResolver(fn () => '');
    }

    /**
     * Register the translation line loader.
     *
     * @return void
     */
    protected function registerTranslationLoader(string $path)
    {
        $fileLoader = new FileLoader($this->app->get('files'), $path);
        $this->app->singleton('translation.loader', static fn () => $fileLoader);

        $this->app->singleton('translator', function ($app) use ($fileLoader) {
            // When registering the translator component, we'll need to set the default
            // locale as well as the fallback locale. So, we'll grab the application
            // configuration, so we can easily get both of these values from there.
            $locale = $app['config']['app.locale'];

            $trans = new Translator($fileLoader, $locale);

            $trans->setFallback($app['config']['app.fallback_locale']);

            return $trans;
        });
    }

    public function boot()
    {
        $laravelPaginationProviderReflection = new ReflectionClass(PaginationServiceProvider::class);
        $paginationBaseDir = dirname($laravelPaginationProviderReflection->getFileName());

        unset($laravelPaginationProviderReflection);
        $this->loadViewsFrom($paginationBaseDir . '/resources/views', 'pagination');
        $path = realpath(KickflipHelper::rootPackagePath() . '/resources/lang');
        $this->loadTranslationsFrom($path, 'kickflip');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                $paginationBaseDir . '/resources/views' => $this->app->resourcePath('views/vendor/pagination'),
            ], 'laravel-pagination');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['translator', 'translation.loader'];
    }
}
