<?php

declare(strict_types=1);

namespace KickflipMonoTests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Kickflip\KickflipHelper;
use LaravelZero\Framework\Testing\TestCase as BaseTestCase;

abstract class DocsTestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        /**
         * @var \LaravelZero\Framework\Application $app
         */
        $app = require __DIR__ . '/../packages/kickflip-cli/bootstrap/app.php';
        KickflipHelper::setPaths(KickflipHelper::basePath(__DIR__ . '/../packages/kickflip-docs'));
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
