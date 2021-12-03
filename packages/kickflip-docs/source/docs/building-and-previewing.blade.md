When you'd like to generate your site, run the build command from within your project root:

```php
$ ./vendor/bin/kickflip build
```

Kickflip will generate your static HTML and place it in the `/build_local` directory by default.
When compiling for non-dev environments the build directory will change to reflect.

Using the default structure, `/build_local` will look like:

![An example of kickflips default build_local output!]({{ kickflip_asset_url('img/build-local-example.png') }})

## Previewing with Valet
The easiest way to preview a Kickflip site would be to simply use [Laravel Valet](https://laravel.com/docs/8.x/valet).
To get this working, simply set up your project as a Valet site then build the site.

Once the build has completed and a `/build_local` directory exists Valet will resolve the site content.