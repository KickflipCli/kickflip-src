<?php

declare(strict_types=1);

namespace Kickflip\Listeners;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kickflip\KickflipHelper;
use samdark\sitemap\Sitemap;

use function collect;
use function rtrim;
use function sprintf;
use function str_replace;
use function time;

use const PHP_EOL;

/**
 * This sitemap generator example works based on analyzing the output HTML files.
 * The listener here should be fired when \Kickflip\Events\SiteBuildComplete::class
 *
 * Consider alternative implementations that work based on PageData's of a site for your use-case.
 * That could either be done by:
 * - creating a sitemap service that listens for
 *      PageDataCreated events (collect pages) and the SiteBuildComplete (write sitemap), or
 * - simply using the SourcesLocator class to get a list of the pages.
 * The downside of that route is missing any already static HTML/txt files copied from the source folder.
 */
final class GenerateSitemap
{
    /**
     * @var array|string[]
     */
    protected array $exclude = [
        '/assets/*',
        '*/favicon.ico',
        '*/404*',
    ];

    public function handle(): void
    {
        $kickflipConfig = KickflipHelper::config();
        $baseUrl = $kickflipConfig->get('site.baseUrl');
        $outputBaseDir = $kickflipConfig->get('paths.build.destination');
        $sitemap = new Sitemap($outputBaseDir . '/sitemap.xml');

        collect($this->getOutputPaths((string) $outputBaseDir))
            ->reject(fn ($path) => $this->isExcluded($path))
            ->map(fn ($path) => str_replace('index.html', '', $path))
            ->map(fn ($path) => sprintf(
                '%s%s',
                rtrim($baseUrl, '/'),
                str_replace('\\', '/', $path),
            ))
            ->sort()
            ->each(function ($path) use ($sitemap) {
                $sitemap->addItem($path, time(), Sitemap::DAILY);
            });

        $sitemap->write();
    }

    public function isExcluded(string $path): bool
    {
        return Str::is($this->exclude, $path);
    }

    /**
     * @return string[]
     */
    private function getOutputPaths(string $outputBaseDir): array
    {
        /**
         * @var FilesystemAdapter|Filesystem $localFilesystem
         */
        $localFilesystem = Storage::disk('local');
        $relativeDir = Str::of($outputBaseDir)
                        ->after($localFilesystem->path(''));

        return collect($localFilesystem->allFiles($relativeDir))
                        ->map(static fn ($value) => Str::after($value, $relativeDir))
                        ->toArray();
    }
}
