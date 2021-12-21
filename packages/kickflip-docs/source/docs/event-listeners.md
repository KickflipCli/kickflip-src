Ultimately since Kickflip was inspired by Jigsaw, we provide the same basic events that Jigsaw uses.
These can be used to run custom code before and after your build is processed.

There are 3 supported events:
- A `SiteBuildStarted` event is fired before any `source` files have been processed.
This provides an opportunity to programmatically modify settings, fetch data, or modify files in the `source` folder.
- A `SiteBuildComplete` event is fired after the build is complete, and all the output files have been written.
This provides a chance to take care of any post-processing steps (like generating a `sitemap.xml` file).

---

## Registering an event listener

To add a listener to one of these events simply edit `config/bootstrap.php`.
Within that file you can use Laravel's `Event` facade to register listeners like:

> config/bootstrap.php

```php
use KickflipDocs\Listeners\GenerateSitemap;
use Illuminate\Support\Facades\Event;
use Kickflip\Events\SiteBuildComplete;

Event::listen(SiteBuildComplete::class, GenerateSitemap::class);
```

Just like any Laravel based Event listener you can even register a simple closure.

> See it in Action: The example above produces the Sitemap of this site, see here: [sitemap.xml](/sitemap.xml).

Read about this in the Laravel docs under, [Manually Registering Events](https://laravel.com/docs/master/events#manually-registering-events).