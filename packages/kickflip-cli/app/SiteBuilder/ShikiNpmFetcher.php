<?php

namespace Kickflip\SiteBuilder;

use Composer\InstalledVersions;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class ShikiNpmFetcher
{
    public function markdownHighlighterEnabled(): bool
    {
        return config('markdown.code_highlighting.enabled');
    }

    public function getProjectRootDirectory(): string
    {
        static $projectRootDirectory;
        if (!isset($projectRootDirectory)) {
            $reflection = new \ReflectionClass(InstalledVersions::class);
            $projectRootDirectory = dirname($reflection->getFileName(), 3);
        }
        return $projectRootDirectory;
    }

    public function isShikiInstalled(): bool
    {
        return file_exists($this->getProjectRootDirectory() . '/node_modules') &&
                file_exists($this->getProjectRootDirectory() . '/node_modules/shiki');
    }

    public function installShiki()
    {
        $command = [
            (new ExecutableFinder)->find('npm', 'npm', [
                '/usr/local/bin',
                '/opt/homebrew/bin',
            ]),
            'install',
            'shiki',
        ];

        $process = new Process(
            command: $command,
            cwd: $this->getProjectRootDirectory(),
            timeout: null,
        );

        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }
}
