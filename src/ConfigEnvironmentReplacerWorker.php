<?php

declare(strict_types=1);
namespace App;

use Nette\Utils\Strings;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;

final class ConfigEnvironmentReplacerWorker implements ReleaseWorkerInterface
{
    /**
     * @inheritDoc
     */
    public function work($version): void
    {
        $configFile = \getcwd() . '/config/app.php';
        if (!\file_exists($configFile)) {
            return;
        }
        $configFileContent = \file_get_contents($configFile);
        $configFileContent = Strings::replace($configFileContent, "/'env' => '([a-z]+)'/", "'env' => 'production'");
        \file_put_contents($configFile, $configFileContent);
    }

    /**
     * @inheritDoc
     */
    public function getDescription($version): string
    {
        return 'Update config/app.php to production environment';
    }
}
