<?php

declare(strict_types=1);

namespace Kickflip\RouterNavPlugin\Listeners;

use Illuminate\Config\Repository;
use Illuminate\Support\Facades\View;
use Kickflip\KickflipHelper;
use Kickflip\SiteBuilder\SourcesLocator;

/**
 * An event handler that will listen for SiteBuildStarted events.
 *
 * This find a `navigation.php` config file and load it into KickflipCLI state and as a View variable.
 */
class SetupSiteNav
{
    public function handle()
    {
        /**
         * @var Repository $kickflipCliState
         */
        $kickflipCliState = KickflipHelper::config();
        $navConfigPath = KickflipHelper::configPath('navigation.php');
        app()->make(SourcesLocator::class); // This forces the singleton to be initialized, must be done after Pretty URL setting loaded

        // Load base nav config into state
        if (file_exists($navConfigPath)) {
            $navConfig = include $navConfigPath;
            $kickflipCliState->set('siteNav', $navConfig);
        }

        // Share site nav into global View data...
        View::share(
            'navigation',
            $kickflipCliState->get('siteNav')
        );
    }
}