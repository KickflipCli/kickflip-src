<?php

namespace KickflipMono;

use Nette\Utils\Strings;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;

class EnvironmentReplacerWorker implements ReleaseWorkerInterface
{
    /**
     * @inheritDoc
     */
    public function work($version): void
    {
        $configFile = \getcwd() . '/packages/kickflip-cli/config/app.php';
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
        return 'Replace environment config value for release';
    }
}
