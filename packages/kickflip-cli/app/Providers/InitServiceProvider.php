<?php

namespace Kickflip\Providers;

use Illuminate\Config\Repository;
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
        $kickflipCliState = $this->app->get('kickflipCli');
        # Set a relevant paths
        $cwd = getcwd();
        $this->app->instance('cwd', $cwd);
        $kickflipCliState->set('paths', [
            'cache' => $cwd . '/cache',
            'config' => $cwd . '/config/config.php',
            'env_config' => $cwd . '/config/config.{env}.php',
            'bootstrapFile' => $cwd . '/config/bootstrap.php',
            'navigationFile' => $cwd . '/config/navigation.php',
            'env_navigationFile' => $cwd . '/config/navigation.{env}.php',
            'build' => [
                'source' => $cwd . '/source',
                'destination' => $cwd . '/build_{env}',
            ]
        ]);

        /**
         * @var Repository $config
         */
        $config = app('config');
        $config->set('view.paths', [
            kickflip_path('resources/views'),
            kickflip_path('source'),
        ]);
        $config->set('view.compiled', $kickflipCliState->get('paths.cache'));
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
