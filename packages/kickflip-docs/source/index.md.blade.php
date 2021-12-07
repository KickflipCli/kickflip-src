---
title: Getting Started
---
Kickflip is a Laravel-based open-source framework for creating static content based websites.
It's great for building basic portfolio sites, simple blogs, or documentation sites.

# Meet Kickflip

## System Requirements
To run Kickflip you need to use PHP 8.0 or newer and Composer needs to be setup.
You will also need, `node.js` and NPM to run _Laravel Mix_ and install code-highlight dependencies.

## Built With
The core of Kickflip is actually built off of [Laravel Zero](https://laravel-zero.com/) to provide the basic CLI.
This also allows Kickflip to use Laravel's Blade as a primary renderer too.
A huge benefit of this is that any Blade based packages for Laravel can be used in Kickflip too.

### Service Providers
Using service providers in Kickflip is just as common as using them in any other Laravel based app.
The main difference will be how Kickflip helps you register those service providers.

Simply add the service providers class to the `providePackages` value in the config file:

```php
    // Composer packages with laravel providers to load
    'providePackages' => [
        BladeIconsServiceProvider::class,
        BladeBoxiconsServiceProvider::class,
    ],
```

Read more on the [Environments]({{ getDocUrl('building-and-previewing-environments') }}) page.
