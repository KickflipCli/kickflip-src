---
extends: layouts.documentation
section: docs_content
title: Navigation
---

Ultimately the concern of creating a site navigation will be something for your project to implement.
There are a lot of good solutions for building menu's in Laravel and other great options for generating HTML.

Kickflip CLI does aid in this by being aware of a Navigation config file located at `config/navigation.php`.
This will subsequently be stored in the Kickflip CLI state as the `SiteData::$navigation` property.

## Suggestions

The concern of setting up and managing navigation state isn't something Kickflip handles for you.
You should be able to build navigation menus however you're most comfortable.

That in mind, here are some suggestions to try:

### Mimic the Docs Site
Our Docs website creates a `NavItem` object and builds the main side navigation using those.
This is simply done in the `config/navigation.php` file like:

```php
return [
    NavItem::make('Getting Started', '/docs/getting-started')
        ->setChildren([
            NavItem::make('Installation', '/docs/getting-started-install'),
        ]),
    NavItem::make('Event Listeners', '/docs/event-listeners'),
    NavItem::make('Navigation', '/docs/navigation'),
];
```

Then the views used to render the layouts and navigation utilize the public methods on the `NavItem::class`.

So while the global `SiteData` will contain the navigation array it won't enforce any specific types or shape.
In the end, your site's view templates will need to know how to render the navigation data your site configures.
