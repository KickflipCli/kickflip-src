<?php

declare(strict_types=1);

use Kickflip\RouterNavPlugin\Models\NavItem;

return [
    NavItem::makeFromRouteName('Getting Started', 'docs.getting-started')
        ->setChildren([
            NavItem::make('Installation', route('docs.getting-started-installation')),
            NavItem::make('Directory Structure', route('docs.getting-started-structure')),
        ]),
    NavItem::make('Building & Previewing', route('docs.building-and-previewing'))
        ->setChildren([
            NavItem::make('Environments', route('docs.building-and-previewing-environments')),
            NavItem::make('Compiling Assets', route('docs.building-and-previewing-compiling-assets')),
        ]),
    NavItem::make('Creating your Site\'s Content', route('docs.site-content'))
        ->setChildren([
            NavItem::make('Templates & Components', route('docs.site-content-templates-and-components')),
            NavItem::make('Markdown', url('#docs.site-content-markdown')),
            NavItem::make('Other File Types', url('#docs.site-content-other-file-types')),
        ]),
    NavItem::make('Template Variables', route('docs.template-variables'))
        ->setChildren([
            NavItem::make('Site Variables', route('docs.template-variables-site')),
            NavItem::make('Page Data', route('docs.template-variables-page')),
        ]),
    NavItem::make('Pretty URLs', url('#docs.pretty-urls')),
    NavItem::make('Event Listeners', route('docs.event-listeners')),
    NavItem::make('Navigation', route('docs.navigation')),
];
