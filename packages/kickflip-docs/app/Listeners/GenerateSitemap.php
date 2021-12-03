<?php

namespace KickflipDocs\Listeners;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kickflip\KickflipHelper;
use samdark\sitemap\Sitemap;

class GenerateSitemap
{
    protected $exclude = [
        '/assets',
        '/assets/*',
        '*/favicon.ico',
        '*/404'
    ];

    public function handle()
    {
        $kickflipConfig = KickflipHelper::config();
        $baseUrl = $kickflipConfig->get('site.baseUrl');
        $outputBaseDir = $kickflipConfig->get('paths.build.destination');

        if (! $baseUrl) {
            echo("\nTo generate a sitemap.xml file, please specify a 'baseUrl' in config.php.\n\n");

            return;
        }

        $sitemap = new Sitemap($outputBaseDir . '/sitemap.xml');

        collect($this->getOutputPaths((string) $outputBaseDir))
            ->reject(function ($path) {
                return $this->isExcluded($path);
            })->push('/')->sort()
            ->each(function ($path) use ($baseUrl, $sitemap) {
                $sitemap->addItem(rtrim($baseUrl, '/') . $path, time(), Sitemap::DAILY);
        });

        $sitemap->write();
    }

    public function isExcluded($path)
    {
        return Str::is($this->exclude, $path);
    }

    private function getOutputPaths(string $outputBaseDir): array
    {
        $localFilesystem = Storage::disk('local');
        $relativeDir = Str::of($outputBaseDir)->after($localFilesystem->path(''));

        return collect($localFilesystem->allDirectories($relativeDir))
                        ->map(static fn($value) => Str::after($value, $relativeDir))
                        ->toArray();
    }
}
