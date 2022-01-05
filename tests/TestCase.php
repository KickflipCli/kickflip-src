<?php

declare(strict_types=1);

namespace KickflipMonoTests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\File;
use Illuminate\View\Factory;
use Kickflip\KickflipHelper;
use Kickflip\Models\PageData;
use Kickflip\Models\SourcePageMetaData;
use LaravelZero\Framework\Application;
use LaravelZero\Framework\Testing\TestCase as BaseTestCase;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

use function file_exists;
use function file_get_contents;
use function func_get_args;

use const DIRECTORY_SEPARATOR;

abstract class TestCase extends BaseTestCase
{
    use PlatformAgnosticHelpers;
    use MatchesSnapshots;

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
         * @var Application $app
         */
        $app = require __DIR__ . '/../packages/kickflip-cli/bootstrap/app.php';
        KickflipHelper::setPaths(KickflipHelper::basePath(__DIR__ . self::agnosticPath('/../packages/kickflip')));
        $app->make(Kernel::class)->bootstrap();
        $this->callAfterResolving($app, 'view', function ($view) {
            /**
             * @var Factory $view
             */
            $view->addLocation(__DIR__ . self::agnosticPath('/views'));
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
            cwd: __DIR__ . self::agnosticPath('/../packages/kickflip'),
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
        $splFileInfo = File::files(__DIR__ . DIRECTORY_SEPARATOR . 'sources')[$index];
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
}
