<?php

declare(strict_types=1);

namespace Kickflip\Providers;

use Illuminate\Pagination\PaginationServiceProvider;
use Illuminate\Support\ServiceProvider;
use Kickflip\View\KickflipPaginator;
use ReflectionClass;

use function dirname;

class CustomPaginatorServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        KickflipPaginator::viewFactoryResolver(fn () => $this->app->get('view'));

        KickflipPaginator::currentPathResolver(fn () => $this->app->get('kickflipCli')->get('page')->getUrl());

        KickflipPaginator::queryStringResolver(fn () => '');
    }

    public function boot()
    {
        $laravelPaginationProviderReflection = new ReflectionClass(PaginationServiceProvider::class);
        $paginationBaseDir = dirname($laravelPaginationProviderReflection->getFileName());

        unset($laravelPaginationProviderReflection);
        $this->loadViewsFrom($paginationBaseDir . '/resources/views', 'pagination');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'courier');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                $paginationBaseDir . '/resources/views' => $this->app->resourcePath('views/vendor/pagination'),
            ], 'laravel-pagination');
        }
    }
}
