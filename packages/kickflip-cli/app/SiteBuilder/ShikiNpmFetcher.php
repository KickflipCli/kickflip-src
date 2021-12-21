<?php

namespace Kickflip\SiteBuilder;

use Composer\InstalledVersions;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\ShikiPhp\Shiki;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * This class is responsible for fetching Shiki to ensure code highlighting always works
 */
final class ShikiNpmFetcher
{
    private string $projectRootDirectory;
    private Filesystem $projectRootDirectoryFilesystem;
    private bool $isNpmUsedByProject;

    public function __construct()
    {
        // Determine the root folder based on the composer vendor dir in use.
        $reflection = new \ReflectionClass(InstalledVersions::class);
        $this->projectRootDirectory = dirname($reflection->getFileName(), 3);
        unset($reflection);

        // Init a filesystem object for the project root directory.
        config()->set('filesystems.disks.arbitrary.root', $this->projectRootDirectory);
        $this->projectRootDirectoryFilesystem = Storage::disk('arbitrary');

        // Collect this on init since we'll DL shiki no matter what - this way we know if we should clean up later.
        $this->isNpmUsedByProject = $this->projectRootDirectoryFilesystem->exists('package.json');
    }

    /**
     * @throws \Exception
     */
    public function getProjectRootDirectory(): string
    {
        return $this->projectRootDirectory;
    }

    public function isNpmUsedByProject(): bool
    {
        return $this->isNpmUsedByProject;
    }

    /**
     * Determine if NPM's package.json exists and if shiki is in either dependencies or devDependencies.
     *
     * @return bool
     * @throws \Exception
     */
    public function isShikiRequired(): bool
    {
        return $this->projectRootDirectoryFilesystem->exists('package.json') &&
            ($rootNpmPackages = json_decode(file_get_contents($this->getProjectRootDirectory() . '/package.json'))) &&
                (
                    property_exists($rootNpmPackages, 'dependencies') &&  property_exists($rootNpmPackages->dependencies, 'shiki')||
                    property_exists($rootNpmPackages, 'devDependencies') &&  property_exists($rootNpmPackages->devDependencies, 'shiki')
                );
    }

    /**
     * Determine if Shiki has been downloaded by NPM yet.
     * @return bool
     * @throws \Exception
     */
    public function isShikiDownloaded(): bool
    {
        return $this->projectRootDirectoryFilesystem->exists('node_modules') &&
            $this->projectRootDirectoryFilesystem->exists('node_modules/shiki');
    }

    /**
     * Run the NPM install command with shiki as a dependency.
     * @return string
     * @throws \Exception
     */
    public function installShiki()
    {
        $command = [
            (new ExecutableFinder)->find('npm', 'npm', [
                '/usr/local/bin',
                '/opt/homebrew/bin',
            ]),
            'install',
            '-D',
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

    /**
     * Completely remove node_modules and package.json.
     * @return string
     * @throws \Exception
     */
    public function removeShikiAndNodeModules(): void
    {
        $filesToDelete = [
            'package.json',
            'package-lock.json',
            'node_modules',
        ];
        foreach ($filesToDelete as $file) {
            if ($this->projectRootDirectoryFilesystem->exists($file)) {
                $absolutePath = $this->projectRootDirectoryFilesystem->path($file);
                if (File::isDirectory($absolutePath)) {
                    File::deleteDirectory($absolutePath);
                } else {
                    $this->projectRootDirectoryFilesystem->delete($file);
                }
            }
        }
    }
}
