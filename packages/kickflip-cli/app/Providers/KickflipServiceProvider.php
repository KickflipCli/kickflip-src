<?php

declare(strict_types=1);

namespace Kickflip\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\View\Factory as ViewFactory;
use Kickflip\Enums\CliStateDirPaths;
use Kickflip\KickflipHelper;
use Kickflip\Logger;
use Illuminate\Support\ServiceProvider;
use Kickflip\SiteBuilder\ShikiNpmFetcher;
use Kickflip\SiteBuilder\SourcesLocator;
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
            app('config')->set('app.url',$kickflipCliState->get('site.baseUrl'));
        }

        # Implement the kickflip level autoloading...
        $packages = KickflipHelper::config('site.providePackages', []);
        if (count($packages) > 0) {
            $this->bootSemiAutoloadProviders($packages);
        }

        $this->app->singleton(ShikiNpmFetcher::class, static fn() => new ShikiNpmFetcher());
        $this->app->singleton(BladeMarkdownEngine::class, function($app) {
            return new BladeMarkdownEngine(
                $app->get('blade.compiler'),
                $app->get(Filesystem::class),
                $app->get(MarkdownRenderer::class)
            );
        });
        $this->app->singleton(MarkdownEngine::class, function($app) {
            return new MarkdownEngine(
                $app->get(Filesystem::class),
                $app->get(MarkdownRenderer::class)
            );
        });
        $this->app->singleton(SourcesLocator::class, function($app) {
            return new SourcesLocator(KickflipHelper::sourcePath());
        });

        // Check for local markdown config file and merge on top if exists...
        $projectMarkdownConfig = sprintf('%s%s%s', dirname(KickflipHelper::namedPath(CliStateDirPaths::Config)), DIRECTORY_SEPARATOR, 'markdown.php');
        if (File::exists($projectMarkdownConfig)) {
            config()->set('markdown', require $projectMarkdownConfig);
            $kickflipMarkdownConfig = KickflipHelper::rootPackagePath() . '/config/markdown.php';
            $this->mergeConfigFrom($kickflipMarkdownConfig, 'markdown');
            $basePackageConfig = dirname((new \ReflectionClass(\Spatie\LaravelMarkdown\MarkdownServiceProvider::class))->getFileName(), 2) .
                '/config/markdown.php';
            $this->mergeConfigFrom($basePackageConfig, 'markdown');
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
        config(['logging.channels.single.path' => KickflipHelper::basePath() . '/kickflip.log']);
        $this->enableBladeMarkdownEngine();
        $bootstrapFile = $this->app->get('kickflipCli')->get('paths.bootstrapFile');
        if (File::exists($bootstrapFile)) {
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
            return $app->get(MarkdownEngine::class);
        });
        $view->addExtension('markdown', 'markdown');

        $view->addExtension('blade.md', 'blademd', function () use ($app) {
            return $app->get(BladeMarkdownEngine::class);
        });
        $view->addExtension('blade.markdown', 'blademd');
        $view->addExtension('md.blade.php', 'blademd');
    }
}
