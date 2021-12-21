<?php

use Kickflip\Models\NavItem;

return [
    NavItem::make('Getting Started', getDocUrl('getting-started'))
        ->setChildren([
            NavItem::make('Installation', getDocUrl('getting-started-installation')),
            NavItem::make('Directory Structure', getDocUrl('getting-started-structure')),
        ]),
    NavItem::make('Building & Previewing', getDocUrl('building-and-previewing'))
        ->setChildren([
            NavItem::make('Environments', getDocUrl('building-and-previewing-environments')),
            NavItem::make('Compiling Assets', getDocUrl('building-and-previewing-compiling-assets')),
        ]),
    NavItem::make('Creating your Site\'s Content', getDocUrl('site-content'))
        ->setChildren([
            NavItem::make('Templates & Components', getDocUrl('site-content-templates-and-components')),
            NavItem::make('Markdown', getDocUrl('site-content-markdown')),
            NavItem::make('Other File Types', getDocUrl('site-content-other-file-types')),
        ]),
    NavItem::make('Template Variables', getDocUrl('template-variables'))
        ->setChildren([
            NavItem::make('Site Variables', getDocUrl('template-variables-site')),
            NavItem::make('Page Data', getDocUrl('template-variables-page')),
        ]),
    NavItem::make('Pretty URLs', getDocUrl('pretty-urls')),
    NavItem::make('Event Listeners', getDocUrl('event-listeners')),
    NavItem::make('Navigation', getDocUrl('navigation')),
];
