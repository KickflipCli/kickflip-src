<?php

declare(strict_types=1);

namespace Kickflip\SiteBuilder;

use Composer\InstalledVersions;
use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ReflectionClass;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

use function config;
use function count;
use function dirname;
use function file_get_contents;
use function json_decode;
use function property_exists;

use const DIRECTORY_SEPARATOR;

/**
 * This class is responsible for fetching Shiki to ensure code highlighting always works
 */
final class NpmFetcher
{
    private string $projectRootDirectory;
    /**
     * @var Filesystem&FilesystemAdapter
     */
    private Filesystem $projectRootDirectoryFilesystem;
    private bool $isNpmUsedByProject;

    /**
     * @param string[] $packages
     */
    public function __construct(
        private array $packages = ['shiki'],
    ) {
        // Determine the root folder based on the composer vendor dir in use.
        $reflection = new ReflectionClass(InstalledVersions::class);
        $this->projectRootDirectory = dirname($reflection->getFileName(), 3);
        unset($reflection);

        // Init a filesystem object for the project root directory.
        config()->set('filesystems.disks.arbitrary.root', $this->projectRootDirectory);
        /**
         * @var Filesystem&FilesystemAdapter $arbitraryDisk
         */
        $arbitraryDisk = Storage::disk('arbitrary');
        $this->projectRootDirectoryFilesystem = $arbitraryDisk;

        // Collect this on init since we'll DL pakages no matter what - this way we know if we should clean up later.
        $this->isNpmUsedByProject = $this->projectRootDirectoryFilesystem->exists('package.json');
    }

    /**
     * @return string[]
     */
    public function packages(): array
    {
        return $this->packages;
    }

    /**
     * @throws Exception
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
     * Determine if NPM's package.json exists and if the configured NPM package(s) is in either dependencies or devDependencies.
     *
     * @throws Exception
     */
    public function isRequired(): bool
    {
        $i = 0;
        $packageCount = count($this->packages);
        $result = true;
        while ($result && $i < $packageCount) {
            $package = $this->packages[$i];
            $result = $this->isRequiredPackage($package) || $this->isRequiredPackageLock($package);
            $i++;
        }

        return $result;
    }

    public function isRequiredPackage(string $package): bool
    {
        if (!$this->projectRootDirectoryFilesystem->exists('package.json')) {
            return false;
        }

        $fileContents = $this->projectRootDirectoryFilesystem->get('package.json');
        $rootNpmPackages = json_decode($fileContents);

        return ($rootNpmPackages !== false) &&
            (
                property_exists($rootNpmPackages, 'dependencies') &&
                property_exists($rootNpmPackages->dependencies, $package)
            ) ||
            (
                property_exists($rootNpmPackages, 'devDependencies') &&
                property_exists($rootNpmPackages->devDependencies, $package)
            );
    }

    public function isRequiredPackageLock(string $package): bool
    {
        $packageJsonLockPath = $this->getProjectRootDirectory() . DIRECTORY_SEPARATOR . 'package-lock.json';

        return $this->projectRootDirectoryFilesystem->exists('package-lock.json') &&
            ($rootNpmPackageLock = json_decode(
                file_get_contents($packageJsonLockPath),
            )) &&
            (
                // V2 Package Lock
                (
                    property_exists($rootNpmPackageLock, 'packages') &&
                    (
                        property_exists($rootNpmPackageLock->packages->{''}, 'dependencies') &&
                        property_exists($rootNpmPackageLock->packages->{''}->dependencies, $package) ||
                        property_exists($rootNpmPackageLock->packages->{''}, 'devDependencies') &&
                        property_exists($rootNpmPackageLock->packages->{''}->devDependencies, $package)
                    )
                ) ||
                // V1 Package Lock
                (
                    property_exists($rootNpmPackageLock, 'dependencies') &&
                    property_exists($rootNpmPackageLock->dependencies, $package) ||
                    property_exists($rootNpmPackageLock, 'devDependencies') &&
                    property_exists($rootNpmPackageLock->devDependencies, $package)
                )
            );
    }

    /**
     * Determine if Shiki has been downloaded by NPM yet.
     */
    public function isDownloaded(): bool
    {
        if (!$this->projectRootDirectoryFilesystem->exists('node_modules')) {
            return false;
        }

        $i = 0;
        $packageCount = count($this->packages);
        $result = true;
        while ($result && $i < $packageCount) {
            $package = $this->packages[$i];
            $result = $this->projectRootDirectoryFilesystem->exists('node_modules' . DIRECTORY_SEPARATOR . $package);
            $i++;
        }

        return $result;
    }

    /**
     * Run the NPM install command with shiki as a dependency.
     *
     * @return string
     *
     * @throws Exception
     */
    public function installPackage(string $package)
    {
        $command = [
            (new ExecutableFinder())->find('npm', 'npm', [
                '/usr/local/bin',
                '/opt/homebrew/bin',
            ]),
            'install',
            '-D',
            $package,
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
     *
     * @throws Exception
     */
    public function removeAndCleanNodeModules(): void
    {
        $filesToDelete = [
            'package.json',
            'package-lock.json',
            'node_modules',
        ];
        foreach ($filesToDelete as $file) {
            if ($this->projectRootDirectoryFilesystem->exists($file)) {
                $this->removePath($file);
            }
        }
    }

    private function removePath(string $filePath): void
    {
        $absolutePath = $this->projectRootDirectoryFilesystem->path($filePath);
        if (File::isDirectory($absolutePath)) {
            File::deleteDirectory($absolutePath);
        } elseif (File::isFile($absolutePath)) {
            File::delete($absolutePath);
        }
    }
}
