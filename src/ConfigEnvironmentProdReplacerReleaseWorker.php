<?php

declare(strict_types=1);

namespace RepoBuilder;

use Nette\Utils\Strings;
use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;

use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function getcwd;

final class ConfigEnvironmentProdReplacerReleaseWorker implements ReleaseWorkerInterface
{
    public function work(Version $version): void
    {
        $configFile = getcwd() . '/packages/kickflip-cli/config/app.php';
        if (!file_exists($configFile)) {
            return;
        }
        $configFileContent = file_get_contents($configFile);
        $match = "/'env' => env\('APP_ENV', '([a-z]+)'\)/";
        $replacement = "'env' => env('APP_ENV', 'production')";
        $configFileContent = Strings::replace($configFileContent, $match, $replacement);
        file_put_contents($configFile, $configFileContent);
    }

    public function getDescription(Version $version): string
    {
        return 'Update config/app.php to production environment';
    }
}
