---
extends: layouts.documentation
section: docs_content
title: Navigation
---

For any website consisting of a more than a few pages creating a consistent navigation experience is important.
As there are many different shapes and sizes of navigation Kickflip does not directly provide Navigation features.

Ultimately the concern of creating and rendering a site navigation is something your site project will manage.
There are already a lot of good solutions for building menu's in Blade templates.

> TIP: Be cautious of which you try to use though as many will rely on dynamic URL generation a static site won't have.

## Suggestions

You should be able to build navigation menus however you're most comfortable.

That in mind, here are some suggestions to try:

### Build one with the "Router Nav plugin"
You can opt to use the official [Router Nav plugin](https://packagist.org/packages/kickflip/kickflip-router-nav-plugin) which provides a pseudo-router for Kickflip.
What this means is that Kickflip will create an instance of Laravel's Router to allow use of Router and URL methods.

This gives users the ability to access familiar methods in templates such as: `url()` and `route()`.
Additionally, this plugin will register an event listener that attempts to finda and load a `config/navigation.php` file.

This navigation config file should return an array of `NavItem` instances which represent the desired Nav tree.
Finally, when rendering a view you can access the `$navigation` variable in your blade templates to access Nav items.

### Mimic the Docs Site
Our Docs website uses the method described above, you can feel free to copy this implementation example.
Here's a shortened excerpt of the Nav config these docs use:
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

Then when rendering the Nav views can access the whole `NavItem` instance.
This can be helpful for calling methods on the item to determine the active page item and similar rendering cases.

So while the global `SiteData` will contain the navigation array it won't enforce any specific types or shape.
In the end, your site's view templates will need to know how to render the navigation data your site configures.
