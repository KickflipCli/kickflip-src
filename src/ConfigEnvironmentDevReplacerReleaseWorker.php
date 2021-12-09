<?php

namespace RepoBuilder;

use Nette\Utils\Strings;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;

final class ConfigEnvironmentDevReplacerReleaseWorker implements ReleaseWorkerInterface
{
    public function work($version): void
    {
        $configFile = \getcwd() . '/packages/kickflip-cli/config/app.php';
        if (!\file_exists($configFile)) {
            return;
        }
        $configFileContent = \file_get_contents($configFile);
        $configFileContent = Strings::replace($configFileContent, "/'env' => '([a-z]+)'/", "'env' => 'development'");
        \file_put_contents($configFile, $configFileContent);
    }

    public function getDescription($version): string
    {
        return 'Revert config/app.php to development environment';
    }
}
