<?php

use Kickflip\Models\NavItem;

return [
    NavItem::make('Getting Started', '/docs/getting-started')
        ->setChildren([
            NavItem::make('Installation', '/docs/project-guidelines'),
            NavItem::make('Directory Structure', '/docs/getting-started-structure'),
        ]),
    NavItem::make('Template Variables', '/docs/template-variables')
        ->setChildren([
            NavItem::make('Site Variables', '/docs/template-variables-site'),
        ]),
    NavItem::make('Navigation', '/docs/navigation'),
];
