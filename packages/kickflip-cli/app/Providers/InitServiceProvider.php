<?php

declare(strict_types=1);

namespace Kickflip\Providers;

use Illuminate\Config\Repository;
use Kickflip\Enums\CliStateDirPaths;
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
        $this->app->instance('cwd', getcwd());
        KickflipHelper::setPaths(KickflipHelper::basePath());

        /**
         * @var Repository $config
         */
        $config = app('config');
        $config->set('view.paths', [
            KickflipHelper::resourcePath('views'),
            KickflipHelper::sourcePath(),
        ]);
        $config->set('view.compiled', KickflipHelper::namedPath(CliStateDirPaths::Cache));
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
