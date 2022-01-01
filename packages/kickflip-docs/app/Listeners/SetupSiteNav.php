<?php

declare(strict_types=1);

namespace KickflipDocs\Listeners;

use Illuminate\Config\Repository;
use Illuminate\Support\Facades\View;
use Kickflip\KickflipHelper;
use Kickflip\SiteBuilder\SourcesLocator;

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
