<?php

declare(strict_types=1);

namespace Kickflip\Listeners;

use Kickflip\SiteBuilder\SourcesLocator;

use function app;

/**
 * An event handler that will listen for SiteBuildStarted events.
 *
 * This find a `navigation.php` config file and load it into KickflipCLI state and as a View variable.
 */
final class FindSources
{
    public function handle()
    {
        /**
         * @var SourcesLocator $sourcesLocator
         */
        $sourcesLocator = app()->make(SourcesLocator::class);
        if (!$sourcesLocator->hasRun()) {
            $sourcesLocator();
        }
    }
}
