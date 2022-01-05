<?php

declare(strict_types=1);

namespace RepoBuilder;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;

use function sprintf;

final class PushTagReleaseWorker implements ReleaseWorkerInterface
{
    private ProcessRunner $processRunner;

    public function __construct(ProcessRunner $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    public function work(Version $version): void
    {
        $this->processRunner->run('git push --tags --no-verify');
    }

    public function getDescription(Version $version): string
    {
        return sprintf('Push "%s" tag to remote repository', $version->getVersionString());
    }
}
