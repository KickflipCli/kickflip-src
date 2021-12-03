<?php

use Kickflip\Models\NavItem;

return [
    NavItem::make('Getting Started', '/docs/getting-started')
        ->setChildren([
            NavItem::make('Installation', '/docs/project-guidelines'),
            NavItem::make('Directory Structure', '/docs/getting-started-structure'),
            // NavItem::make('Using a Starter Project', 'docs/gitlab-ssh-alias'),
        ]),
    NavItem::make('Building & Previewing', '/docs/building-and-previewing')
        ->setChildren([
            NavItem::make('Environments', '/docs/building-and-previewing-environments'),
            NavItem::make('Compiling Assets', '/docs/building-and-previewing-compiling-assets'),
        ]),
    NavItem::make('Creating your Site\'s Content', '/docs/site-content')
        ->setChildren([
            NavItem::make('Templates & Components', '/docs/site-content-templates-and-components'),
            NavItem::make('Markdown', '/docs/site-content-markdown'),
            NavItem::make('Other File Types', '/docs/site-content-other-file-types'),
        ]),
    NavItem::make('Template Variables', '/docs/template-variables')
        ->setChildren([
            NavItem::make('Site Variables', '/docs/template-variables-site'),
            NavItem::make('Page Data', '/docs/template-variables-page'),
        ]),
    NavItem::make('Pretty URLs', '/docs/pretty-urls'),
    NavItem::make('Event Listeners', '/docs/event-listeners'),
    NavItem::make('Navigation', '/docs/navigation'),
];
