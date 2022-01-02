<?php

namespace KickflipMonoTests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\View\Factory;
use Kickflip\KickflipHelper;
use Kickflip\Models\PageData;
use LaravelZero\Framework\Testing\TestCase as BaseTestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
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
        $app = require __DIR__.'/../packages/kickflip-cli/bootstrap/app.php';
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

    protected function callNpmProcess(...$args)
    {
        $command = [
            (new ExecutableFinder)->find('npm', 'npm', [
                '/usr/local/bin',
                '/opt/homebrew/bin',
            ]),
            ...$args,
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

    protected function callAfterResolving($app, $name, $callback)
    {
        $app->afterResolving($name, $callback);

        if ($app->resolved($name)) {
            $callback($app->make($name), $app);
        }
    }
}
