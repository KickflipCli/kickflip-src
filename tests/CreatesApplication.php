<?php

namespace KickflipMonoTests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\View\Factory;
use Illuminate\View\View;
use Kickflip\KickflipHelper;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
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

    protected function callAfterResolving($app, $name, $callback)
    {
        $app->afterResolving($name, $callback);

        if ($app->resolved($name)) {
            $callback($app->make($name), $app);
        }
    }
}
