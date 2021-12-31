<?php

namespace KickflipMonoTests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\View\Factory;
use Illuminate\View\View;
use Kickflip\KickflipHelper;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
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
        // Setup the mock test URL
        $kickflipCli = $app->get('kickflipCli');
        $kickflipCli->set('site.baseUrl', 'http://kickflip.test/');
        app('config')->set('app.url', 'http://kickflip.test/');
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
