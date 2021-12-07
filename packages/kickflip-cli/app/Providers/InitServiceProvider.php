<?php

declare(strict_types=1);

namespace Kickflip\Providers;

use Illuminate\Config\Repository;
use Kickflip\KickflipHelper;
use Kickflip\Logger;
use Illuminate\Support\ServiceProvider;

class InitServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Logger::timing(__METHOD__);
        Logger::debug("Firing " . __METHOD__);
        # Set a relevant paths
        $this->app->instance('cwd', getcwd());
        $baseDir = KickflipHelper::basePath();
        KickflipHelper::setPaths($baseDir);

        /**
         * @var Repository $config
         */
        $config = app('config');
        $config->set('view.paths', [
            KickflipHelper::resourcePath('views'),
            KickflipHelper::path('source'),
        ]);
        $config->set('view.compiled', KickflipHelper::config('paths.cache'));
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Logger::debug("Firing " . __METHOD__);
        Logger::timing(__METHOD__);
    }
}
