<?php

declare(strict_types=1);

namespace Kickflip\RouterNavPlugin\Listeners;

use Illuminate\Config\Repository;
use Illuminate\Support\Facades\View;
use Kickflip\KickflipHelper;
use Kickflip\SiteBuilder\SourcesLocator;

use function app;
use function file_exists;

/**
 * An event handler that will listen for SiteBuildStarted events.
 *
 * This find a `navigation.php` config file and load it into KickflipCLI state and as a View variable.
 */
final class SetupSiteNav
{
    public function handle()
    {
        /**
         * @var Repository $kickflipCliState
         */
        $kickflipCliState = KickflipHelper::config();
        $navConfigPath = KickflipHelper::configPath('navigation.php');

        // This forces the singleton to be initialized, must be done after Pretty URL setting loaded
        app()->make(SourcesLocator::class);

        // Load base nav config into state
        if (file_exists($navConfigPath)) {
            $navConfig = include $navConfigPath;
            $kickflipCliState->set('siteNav', $navConfig);
        }

        // Share site nav into global View data...
        View::share(
            'navigation',
            $kickflipCliState->get('siteNav'),
        );
    }
}
