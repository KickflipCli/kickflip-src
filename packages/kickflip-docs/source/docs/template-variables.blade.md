Every view in Kickflip will be rendered with a few kinds of variables available to it.
The primary variable types are:

- [Site Variables]({{ getDocUrl('template-variables-site') }}),
- [Page Data]({{ getDocUrl('template-variables-page') }}), and
- any [Global Shared Data](#global-shared-data).

## Global Shared Data

Adding data that will be accessible to all views is super easy.
This is mainly because [Laravel's Blade](https://laravel.com/docs/master/blade) makes this really easy.
And because Kickflip uses the same provider for Blade as Laravel things work very similar.

In Kickflip, this is done simply by using `View::share('key', 'value')` just like in Laravel.
The proper place to add these into would be in your sites `config/bootstrap.php` file.

For example, Kickflip itself loads the `SiteData::class` instance using:

```php
    View::share(
        'site',
        SiteData::fromConfig(KickflipHelper::config('site', []), KickflipHelper::config('siteNav', []))
    );
```

This bootstrap config file will be loaded in at the very end of Kickflip's `KickflipServiceProvider::boot()` method.
Read more about this idea directly on the Laravel Docs under [Sharing Data With All Views](https://laravel.com/docs/master/views#sharing-data-with-all-views).