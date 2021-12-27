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
