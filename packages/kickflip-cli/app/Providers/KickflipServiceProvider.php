<?php

declare(strict_types=1);

namespace Kickflip\Providers;

use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\View;
use Illuminate\View\Factory as ViewFactory;
use Kickflip\KickflipHelper;
use Kickflip\Logger;
use Illuminate\Support\ServiceProvider;
use Kickflip\Models\SiteData;
use Kickflip\View\Engine\BladeMarkdownEngine;
use Kickflip\View\Engine\MarkdownEngine;
use Spatie\LaravelMarkdown\MarkdownRenderer;

class KickflipServiceProvider extends ServiceProvider
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
        $kickflipCliState = KickflipHelper::config();

        # Correct the public_path helper
        $this->app->instance('path.public', $kickflipCliState->get('paths.build.source'));

        # Load base app config into state
        if (file_exists($configPath = $kickflipCliState->get('paths.config'))) {
            $config = include $configPath;
            $kickflipCliState->set('site', $config);
        }

        # Implement the kickflip level autoloading...
        $packages = KickflipHelper::config('site.providePackages', []);
        if (count($packages) > 0) {
            $this->bootSemiAutoloadProviders($packages);
        }

        # Load base nav config into state
        if (file_exists($navConfigPath = $kickflipCliState->get('paths.navigationFile'))) {
            $navConfig = include $navConfigPath;
            $kickflipCliState->set('siteNav', $navConfig);
        }
    }

    /**
     * @param array<string> $autoloadPackages
     */
    private function bootSemiAutoloadProviders(array $autoloadPackages): void
    {
        Logger::timing(__METHOD__);
        $app = $this->app;
        collect($autoloadPackages)->each(function ($composerPackage) use ($app) {
            $app->register($composerPackage);
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

        $codeHighlightTheme = KickflipHelper::config('site.markdown.code.theme', null);
        if ($codeHighlightTheme !== null) {
            /**
             * @var Repository $appConfig
             */
            $appConfig = $this->app['config'];
            $appConfig->set('markdown.code_highlighting.theme', $codeHighlightTheme);
        }

        # ensure you configure the right channel you use
        config(['logging.channels.single.path' => \Phar::running()
            ? dirname(\Phar::running(false)) . '/logs/kickflip.log'
            : storage_path('logs/kickflip.log')
        ]);
        $this->enableBladeMarkdownEngine();

        View::share(
            'site',
            SiteData::fromConfig(KickflipHelper::config('site', []), KickflipHelper::config('siteNav', []))
        );

        $bootstrapFile = $this->app->get('kickflipCli')->get('paths.bootstrapFile');
        if (file_exists($bootstrapFile)) {
            include $bootstrapFile;
        }
    }

    private function enableBladeMarkdownEngine(): void
    {
        $app = $this->app;
        /**
         * @var ViewFactory $view
         */
        $view = $this->app->get('view');
        $view->addExtension('md', 'markdown', function () use ($app) {
            return new MarkdownEngine($app->get(Filesystem::class), $app->get(MarkdownRenderer::class));
        });
        $view->addExtension('markdown', 'markdown');

        $view->addExtension('blade.md', 'blademd', function () use ($app) {
            return new BladeMarkdownEngine($app->get('blade.compiler'), $app->get(Filesystem::class), $app->get(MarkdownRenderer::class));
        });
        $view->addExtension('blade.markdown', 'blademd');
        $view->addExtension('md.blade.php', 'blademd');
    }
}
