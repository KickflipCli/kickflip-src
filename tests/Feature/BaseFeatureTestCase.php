<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\View\Component;
use Illuminate\View\Factory;
use Kickflip\Application;
use Kickflip\KickflipHelper;
use Kickflip\KickflipKernel;
use Kickflip\Models\PageData;
use Kickflip\Models\SourcePageMetaData;
use Kickflip\SiteBuilder\SourcesLocator;
use KickflipMonoTests\PlatformAgnosticHelpers;
use LaravelZero\Framework\Testing\TestCase as BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

use function app;
use function file_exists;
use function file_get_contents;
use function func_get_args;
use function libxml_use_internal_errors;
use function realpath;

abstract class BaseFeatureTestCase extends BaseTestCase
{
    use PlatformAgnosticHelpers;
    use MatchesSnapshots;

    public bool $shouldRunShikiFetcher = true;
    public string $manifestPath = '/source/assets/build/mix-manifest.json';

    public function basePath(): string
    {
        return realpath(__DIR__ . self::agnosticPath('/../../packages/kickflip'));
    }

    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        // Reset things to defaults...
        libxml_use_internal_errors(true);
        Application::$localBase = null;
        // Reset PageData to defaults
        PageData::$defaultExtendsView = 'layouts.master';
        PageData::$defaultExtendsSection = 'body';
        // End resets

        $basePath = $this->basePath();
        if ($this->shouldRunShikiFetcher) {
            if (!file_exists($basePath . $this->manifestPath)) {
                $this->callNpmProcess('install');
                $this->callNpmProcess('run', 'prod');
            }
        }
        /**
         * @var Application $app
         */
        $app = require __DIR__ . '/../../packages/kickflip-cli/bootstrap/app.php';
        KickflipHelper::basePath($basePath);
        KickflipHelper::setPaths($basePath);
        /**
         * @var KickflipKernel $kernel
         */
        $kernel = $app->make(Kernel::class);
        $kernel->bootstrap();
        $this->callAfterResolving($app, 'view', function ($view) {
            /**
             * @var Factory $view
             */
            $view->addLocation(realpath(__DIR__ . self::agnosticPath('/../views')));
        });

        return $app;
    }

    protected function callNpmProcess(): string
    {
        $command = [
            (new ExecutableFinder())->find('npm', 'npm', [
                '/usr/local/bin',
                '/opt/homebrew/bin',
            ]),
            ...func_get_args(),
        ];

        $process = new Process(
            command: $command,
            cwd: __DIR__ . self::agnosticPath('/../../packages/kickflip'),
            timeout: null,
        );

        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    public function getTestPageData(int $index = 0): PageData
    {
        // Fetch a single Symfony SplFileInfo object
        $splFileInfo = KickflipHelper::getFiles(__DIR__ . self::agnosticPath('/../sources'))[$index];
        // Create a SourcePageMetaData object
        $sourcePageMetaData = SourcePageMetaData::fromSplFileInfo($splFileInfo);
        // Parse out the front matter page metadata
        $frontMatterData = KickflipHelper::getFrontMatterParser()
                ->parse(file_get_contents($sourcePageMetaData->getFullPath()))
                ->getFrontMatter() ?? [];

        // Create a PageData object
        return PageData::make($sourcePageMetaData, $frontMatterData);
    }

    protected function callAfterResolving(Application $app, string $name, callable $callback): void
    {
        $app->afterResolving($name, $callback);

        if ($app->resolved($name)) {
            $callback($app->make($name), $app);
        }
    }

    protected function initAndFindSources()
    {
        $sourcesLocator = app(SourcesLocator::class);
        if (!$sourcesLocator->hasRun()) {
            $sourcesLocator();
        }
    }

    protected function tearDown(): void
    {
        Component::flushCache();
        Component::forgetComponentsResolver();
        Component::forgetFactory();
        parent::tearDown();
    }
}
