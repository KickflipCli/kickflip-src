<?php

declare(strict_types=1);

namespace RepoBuilder;

use MonorepoBuilder20211216\Symplify\PackageBuilder\Parameter\ParameterProvider;
use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;
use Symplify\MonorepoBuilder\ValueObject\Option;
use Throwable;

use function sprintf;

final class TagVersionReleaseWorker implements ReleaseWorkerInterface
{
    /*
     * Relates to the wait time needed to help prevent overlapping CI jobs.
     * Currently set to the estimated time for just CS/Security checks.
     */
    protected const WAIT_FOR = 60 * 2;
    private string $branchName;
    private ProcessRunner $processRunner;

    public function __construct(ProcessRunner $processRunner, ParameterProvider $parameterProvider)
    {
        $this->processRunner = $processRunner;
        $this->branchName = $parameterProvider->provideStringParameter(Option::DEFAULT_BRANCH_NAME);
    }

    public function work(Version $version): void
    {
        try {
            $gitAddCommitCommand = 'git add . && git commit --no-verify -m "prepare release"';
            $gitAddCommitCommand .= sprintf(' && git push --no-verify origin "%s"', $this->branchName);
            $this->processRunner->run($gitAddCommitCommand);
        } catch (Throwable $exception) {
            // nothing to commit
        }
        $this->processRunner->run('git tag ' . $version->getOriginalString());
        SleepBuddy::sleepFor(TagVersionReleaseWorker::WAIT_FOR);
    }

    public function getDescription(Version $version): string
    {
        return sprintf('Add local tag "%s"', $version->getOriginalString());
    }
}
