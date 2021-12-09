<?php

declare(strict_types=1);

use Nette\Utils\Strings;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\AddTagToChangelogReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushNextDevReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\PushTagReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetCurrentMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetNextMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\TagVersionReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateBranchAliasReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateReplaceReleaseWorker;
use Symplify\MonorepoBuilder\ValueObject\Option;

if (!class_exists('ConfigEnvironmentReplacerReleaseWorker')) {
    final class ConfigEnvironmentReplacerReleaseWorker implements ReleaseWorkerInterface
    {
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

        public function getDescription($version): string
        {
            return 'Update config/app.php to production environment';
        }
    }

}

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::DEFAULT_BRANCH_NAME, 'main');
    $parameters->set(Option::PACKAGE_DIRECTORIES, [__DIR__ . '/packages']);

    $services = $containerConfigurator->services();
    # release workers - in order to execute
    $services->set(UpdateReplaceReleaseWorker::class);
    $services->set(SetCurrentMutualDependenciesReleaseWorker::class);
    $services->set(ConfigEnvironmentReplacerReleaseWorker::class);
    //$services->set(AddTagToChangelogReleaseWorker::class);
    //$services->set(TagVersionReleaseWorker::class);
    //$services->set(PushTagReleaseWorker::class);
    //$services->set(SetNextMutualDependenciesReleaseWorker::class);
    //$services->set(UpdateBranchAliasReleaseWorker::class);
    //$services->set(PushNextDevReleaseWorker::class);
};
