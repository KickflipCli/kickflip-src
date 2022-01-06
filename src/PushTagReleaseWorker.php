<?php

declare(strict_types=1);

namespace RepoBuilder;

use PharIo\Version\Version;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Release\Process\ProcessRunner;

use function sprintf;

final class PushTagReleaseWorker implements ReleaseWorkerInterface
{
    use SleepBuddy;

    /*
     * Relates to the wait time needed to help prevent overlapping CI jobs.
     * Currently set to the estimated time to delay the next push until the first push's Unit Tests are part way done.
     */
    protected const WAIT_TIME = 60 * 3;

    private ProcessRunner $processRunner;

    public function __construct(ProcessRunner $processRunner)
    {
        $this->processRunner = $processRunner;
    }

    public function work(Version $version): void
    {
        $this->processRunner->run('git push --tags --no-verify');
        self::sleepFor(self::WAIT_TIME);
    }

    public function getDescription(Version $version): string
    {
        return sprintf('Push "%s" tag to remote repository', $version->getVersionString());
    }
}
