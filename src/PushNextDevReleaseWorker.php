<?php

declare(strict_types=1);

namespace RepoBuilder;

use MonorepoBuilder20211216\Symplify\PackageBuilder\Parameter\ParameterProvider;
use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;
use Symplify\MonorepoBuilder\Utils\VersionUtils;
use Symplify\MonorepoBuilder\ValueObject\Option;

use function sprintf;

final class PushNextDevReleaseWorker implements ReleaseWorkerInterface
{
    private string $branchName;
    private ProcessRunner $processRunner;
    private VersionUtils $versionUtils;

    public function __construct(
        ProcessRunner $processRunner,
        VersionUtils $versionUtils,
        ParameterProvider $parameterProvider
    ) {
        $this->processRunner = $processRunner;
        $this->versionUtils = $versionUtils;
        $this->branchName = $parameterProvider->provideStringParameter(Option::DEFAULT_BRANCH_NAME);
    }

    public function work(Version $version): void
    {
        $versionInString = $this->getVersionDev($version);
        $gitAddCommitCommand = 'git add .';
        $gitAddCommitCommand .= sprintf(' && git commit --no-verify --allow-empty -m "open %s"', $versionInString);
        $gitAddCommitCommand .= sprintf(' && git push --no-verify origin "%s"', $this->branchName);
        $this->processRunner->run($gitAddCommitCommand);
    }

    public function getDescription(Version $version): string
    {
        $versionInString = $this->getVersionDev($version);

        return sprintf('Push "%s" open to remote repository', $versionInString);
    }

    private function getVersionDev(Version $version): string
    {
        return $this->versionUtils->getNextAliasFormat($version);
    }
}
