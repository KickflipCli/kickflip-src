<?php

declare(strict_types=1);

namespace KickflipMonoTests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\File;
use Illuminate\View\Factory;
use Kickflip\KickflipHelper;
use Kickflip\Models\PageData;
use Kickflip\Models\SourcePageMetaData;
use LaravelZero\Framework\Testing\TestCase as BaseTestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

use function file_exists;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        // Reset PageData to defaults
        PageData::$defaultExtendsView = 'layouts.master';
        PageData::$defaultExtendsSection = 'body';

        if (!file_exists(__DIR__ . '/../packages/kickflip/source/assets/build/mix-manifest.json')) {
            $this->callNpmProcess('install');
            $this->callNpmProcess('run', 'prod');
        }
        /**
         * @var \LaravelZero\Framework\Application $app
         */
        $app = require __DIR__ . '/../packages/kickflip-cli/bootstrap/app.php';
        KickflipHelper::setPaths(KickflipHelper::basePath(__DIR__ . '/../packages/kickflip'));
        $app->make(Kernel::class)->bootstrap();
        $this->callAfterResolving($app, 'view', function ($view) {
            /**
             * @var Factory $view
             */
            $view->addLocation(__DIR__ . '/views');
        });

        return $app;
    }

    protected function callNpmProcess()
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
            cwd: __DIR__ . '/../packages/kickflip',
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
        $splFileInfo = File::files(__DIR__ . '/sources/')[$index];
        // Create a SourcePageMetaData object
        $sourcePageMetaData = SourcePageMetaData::fromSplFileInfo($splFileInfo);
        // Parse out the frontmatter page meta data
        $frontMatterData = KickflipHelper::getFrontMatterParser()
                ->parse(file_get_contents($sourcePageMetaData->getFullPath()))
                ->getFrontMatter() ?? [];
        // Create a PageData object
        return PageData::make($sourcePageMetaData, $frontMatterData);
    }

    protected function callAfterResolving($app, $name, $callback)
    {
        $app->afterResolving($name, $callback);

        if ($app->resolved($name)) {
            $callback($app->make($name), $app);
        }
    }
}
