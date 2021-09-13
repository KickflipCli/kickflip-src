<?php

namespace Kickflip\Providers;

use Composer\InstalledVersions;
use Kickflip\Logger;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
        # Correct the public_path helper
        $this->app->instance('path.public', $kickflipCliState->get('paths.build.source'));
        # Load base app config into state
        if (file_exists($configPath = $kickflipCliState->get('paths.config'))) {
            $config = include $configPath;
            $kickflipCliState->set('site', $config);
        }

        # Implement the kickflip level autoloading...
        $packages = $this->app->get('kickflipCli')->get('site.providePackages', []);
        if (count($packages) > 0) {
            $this->bootSemiAutoloadProviders($packages);
        }

        # Load base nav config into state
        if (file_exists($navConfigPath = $kickflipCliState->get('paths.navigationFile'))) {
            $navConfig = include $navConfigPath;
            $kickflipCliState->set('siteNav', $navConfig);
        }
    }

    private function bootSemiAutoloadProviders(array $autoloadPackages)
    {
        Logger::timing(__METHOD__);
        $autoloadList = collect($autoloadPackages);
        $app = $this->app;
        $autoloadList->each(function ($composerPackage) use ($app) {
            if (InstalledVersions::getInstallPath($composerPackage)) {
                $packageComposerJson = json_decode(
                    file_get_contents(
                        InstalledVersions::getInstallPath($composerPackage) . DIRECTORY_SEPARATOR . 'composer.json'
                    ),
                    true
                );
                if (isset($packageComposerJson['extra']['laravel']['providers'])) {
                    foreach ($packageComposerJson['extra']['laravel']['providers'] as $serviceProviderClass) {
                        $app->register($serviceProviderClass);
                    }
                }
            }
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Logger::timing(__METHOD__);
        Logger::debug("Firing " . __METHOD__);
        # ensure you configure the right channel you use
        config(['logging.channels.single.path' => \Phar::running()
            ? dirname(\Phar::running(false)) . '/logs/kickflip.log'
            : storage_path('logs/kickflip.log')
        ]);
    }
}
