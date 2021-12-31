<?php

namespace KickflipMonoTests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\View\Factory;
use Illuminate\View\View;
use Kickflip\KickflipHelper;

trait CreatesDocsApplication
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
        KickflipHelper::setPaths(KickflipHelper::basePath(__DIR__ . '/../packages/kickflip-docs'));
        $app->make(Kernel::class)->bootstrap();
        return $app;
    }
}
