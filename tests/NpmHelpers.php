<?php

declare(strict_types=1);

namespace KickflipMonoTests;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

use function getcwd;
use function trim;

trait NpmHelpers
{
    public function getNodeVersion(): string
    {
        $command = [
            (new ExecutableFinder())->find('node', 'node', [
                '/usr/local/bin',
                '/opt/homebrew/bin',
            ]),
            '--version',
        ];

        $process = new Process(
            command: $command,
            cwd: getcwd(),
            timeout: null,
        );
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return trim($process->getOutput());
    }
}
