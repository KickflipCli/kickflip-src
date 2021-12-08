<?php

namespace KickflipTests;

use Illuminate\Contracts\Console\Kernel;
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
        $app = require __DIR__.'/../packages/kickflip-cli/bootstrap/app.php';
        KickflipHelper::setPaths(KickflipHelper::basePath(__DIR__ . '/../packages/kickflip-docs'));
        $app->make(Kernel::class)->bootstrap();
        return $app;
    }
}
