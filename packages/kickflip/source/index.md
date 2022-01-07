---
title: Getting Started
---
# Meet Kickflip
Kickflip is a Laravel-based open-source framework for creating static content based websites.
It's great for building basic portfolio sites, simple blogs, or documentation sites.

## Building your site
You might want to get started by building our your template first, then writing up your content.
This is the suggested route, but really you can do it any way you want!

### Site Template
Your Site's template will be located in the `resources` - this will include the Blade Views, JS and PostCSS/Styles.
Technically you can swap out the style language with anything you like that's supported by Laravel's Mix.

Since the overall structure of this folder is identical to Laravel - if you're a seasoned Laravel user, you should feel right at home!

### Site Content
The site content is all stored within the `source` directory.
Anything in here that's supported by kickflip will be rendered to HTML.
Or if it's in the `source/assets` folder it will be copied into the compiled site.

### Site Page and Asset URLs

Since by default Kickflip is meant to be a CLI based site generator we don't have a real `HttpKernel`.
And as such, we won't have access to Laravel's `UrlGenerator` based `url()` and `route()` helper methods.

This means that your on-site page links will need to be URLs you manage within your project for the most part.
However, a very naive URL helper is built-in as `KickflipHelper::urlFromSource()`.
This method will accept a source file "name" (determined by the file path with `.` instead of `/`).

On this simple site the only other page is the 404 page, so we'll use that as an example:

```blade
The 404 page URL is: {{ KickflipHelper::urlFromSource('404') }}
```

> Results:  The 404 page URL is: /404

> For a "batteries included" experience that allows you to use the `UrlGenerator` based methods, checkout the optional [KickflipCli/kickflip-router-nav-plugin](https://github.com/KickflipCli/kickflip-router-nav-plugin).


However, when it comes to asset URLs we do have access to Mix via a conveniently pre-configured `KickflipHelpers::mix()` and `KickflipHelpers::asset()` methods.
These work the same as they do in Laravel, just pre-configured for Kickflip's folder structure. It all even works without the router nav plugin!

## Using Laravel Packages
Be cautions to not try and use a package that requires PHP to handle logic.
Kickflip can only render HTML sites out of your code, but it will not have a API/Backend!

### Registering Laravel Package Servier Providers
Kickflip will allow you to register packages similar to how in Laravel's `config/app.php` file you can register providers.
Unlike Laravel's `app.providers` config, in kickflip our config file has `providePackages` index.

To use Blade Icons, for example, require a icon package and then update the config as:
```php
    // Composer packages with laravel providers to load
    'providePackages' => [
        BladeIconsServiceProvider::class,
        BladeBoxiconsServiceProvider::class,
    ],
```
You must manually load dependant services too - as such in the example above we load both the general `BladeIconsServiceProvider::class` provider and the icon package.

# Learn More!

To learn more, check out the official [Kickflip Docs](#add-docs-link)!
