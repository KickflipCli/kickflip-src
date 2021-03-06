<?php

declare(strict_types=1);

use RepoBuilder\ConfigEnvironmentDevReplacerReleaseWorker;
use RepoBuilder\ConfigEnvironmentProdReplacerReleaseWorker;
use RepoBuilder\PushNextDevReleaseWorker;
use RepoBuilder\PushTagReleaseWorker;
use RepoBuilder\TagVersionReleaseWorker;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\AddTagToChangelogReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetCurrentMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\SetNextMutualDependenciesReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateBranchAliasReleaseWorker;
use Symplify\MonorepoBuilder\Release\ReleaseWorker\UpdateReplaceReleaseWorker;
use Symplify\MonorepoBuilder\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::DEFAULT_BRANCH_NAME, 'main');
    $parameters->set(Option::PACKAGE_DIRECTORIES, [__DIR__ . '/packages']);

    $services = $containerConfigurator->services();
    // release workers - in order to execute
    $services->set(UpdateReplaceReleaseWorker::class);
    $services->set(SetCurrentMutualDependenciesReleaseWorker::class);
    $services->set(AddTagToChangelogReleaseWorker::class);

    // Update kickflip config to production mode
    $services->set(ConfigEnvironmentProdReplacerReleaseWorker::class);

    $services->set(TagVersionReleaseWorker::class);
    $services->set(PushTagReleaseWorker::class);
    $services->set(SetNextMutualDependenciesReleaseWorker::class);

    // Update kickflip config to production mode
    $services->set(ConfigEnvironmentDevReplacerReleaseWorker::class);

    $services->set(UpdateBranchAliasReleaseWorker::class);
    $services->set(PushNextDevReleaseWorker::class);
};
