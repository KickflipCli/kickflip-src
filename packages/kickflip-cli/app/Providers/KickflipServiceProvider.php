<?php

declare(strict_types=1);

namespace Kickflip\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
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
        $this->app->instance('path.public', KickflipHelper::namedPath(CliStateDirPaths::BuildSource));

        # Load base app config into state
        if (file_exists($configPath = KickflipHelper::namedPath(CliStateDirPaths::ConfigFile))) {
            $config = include $configPath;
            $kickflipCliState->set('site', $config);
            $baseUrl = $kickflipCliState->get('site.baseUrl');
            if ($baseUrl === '') {
                $baseUrl = (string) Str::of($kickflipCliState->get('site.baseUrl'))->rtrim('/')->append('/');
                $kickflipCliState->set('site.baseUrl', $baseUrl);
            }
            app('config')->set('app.url', $baseUrl);
        }

        # Implement the kickflip level autoloading...
        $packages = KickflipHelper::config('site.providePackages', []);
        if (count($packages) > 0) {
            $this->registerSemiAutoloadProviders($packages);
        }

        $this->app->singleton(ShikiNpmFetcher::class, static fn() => new ShikiNpmFetcher());
        $this->registerBladeEngines();
        $this->app->singleton(SourcesLocator::class, function($app) {
            return new SourcesLocator(KickflipHelper::sourcePath());
        });
        $this->loadProjectMarkdownConfig();

    }

    /**
     * @param array<class-string> $autoloadPackages
     */
    private function registerSemiAutoloadProviders(array $autoloadPackages): void
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
        $bootstrapFile = KickflipHelper::namedPath(CliStateDirPaths::BootstrapFile);
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

    /**
     * @return void
     */
    private function registerBladeEngines(): void
    {
        $this->app->singleton(BladeMarkdownEngine::class, function ($app) {
            return new BladeMarkdownEngine(
                $app->get('blade.compiler'),
                $app->get(Filesystem::class),
                $app->get(MarkdownRenderer::class)
            );
        });
        $this->app->singleton(MarkdownEngine::class, function ($app) {
            return new MarkdownEngine(
                $app->get(Filesystem::class),
                $app->get(MarkdownRenderer::class)
            );
        });
    }

    /**
     * @return void
     */
    private function loadProjectMarkdownConfig(): void
    {
        // Check for local markdown config file and merge on top if exists...
        $projectMarkdownConfig = implode(DIRECTORY_SEPARATOR, [
            dirname(KickflipHelper::namedPath(CliStateDirPaths::ConfigFile)),
            'markdown.php',
        ]);
        if (File::exists($projectMarkdownConfig)) {
            // First load and set the project level config..
            config()->set('markdown', require $projectMarkdownConfig);
            // Then load the kickflip CLI default configs...
            $kickflipMarkdownConfig = KickflipHelper::rootPackagePath() . '/config/markdown.php';
            $this->mergeConfigFrom($kickflipMarkdownConfig, 'markdown');
            // Finally load the Spatie default configs...
            $basePackageConfig = dirname((new \ReflectionClass(\Spatie\LaravelMarkdown\MarkdownServiceProvider::class))->getFileName(), 2) .
                '/config/markdown.php';
            $this->mergeConfigFrom($basePackageConfig, 'markdown');
        }
    }
}
