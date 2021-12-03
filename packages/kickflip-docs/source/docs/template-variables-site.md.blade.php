---
extends: layouts.documentation
section: docs_content
title: Site Variables
---

Every rendered view will have the same instance of `SiteData` exposed to it. This is the same one bound to the container on `'site'`.
This object contains properties for all the main site configuration data.

## Config Based Properties
A number of the `config.php` values are defined properties on the `SiteData` object.
For these items the values will be set from the relevant config file directly.

On the config, these will be:
```php
    'baseUrl' => 'http://kickflip-docs.test/',
    'production' => false,
    'siteName' => 'Kickflip',
    'siteDescription' => 'Static site generation based on Laravel Zero',
    // Algolia DocSearch credentials
    'docsearchApiKey' => '',
    'docsearchIndexName' => '',
```

And within the `SiteData` properties they will end up as:
```php
    public string $baseUrl,
    public bool $production,
    public string $siteName,
    public string $siteDescription,
    public ?string $docsearchApiKey = null,
    public ?string $docsearchIndexName = null,
    public bool $autoExtendMarkdown = true,
```

Additionally, the site's navigation data is loaded on to the `SiteData::$navigation` property.
However, this data is configured in `config/navigation.php` rather than in the main config file.

To read more on this, see the [Navigation page]({{ getDocUrl('building-and-previewing-navigation') }}).

## Additional Dynamic Properties

This is not a feature currently supported by Kickflip.
Opinionated as it may be, this was intentional to ensure `SiteData` is very well-defined.
While users familiar with Jigsaw may wish for this - because Kickflip provides other alternatives this will never be supported.

To read more about, providing global data to all Blade powered views see [Global Data]({{ getDocUrl('template-data', 'global-data') }}) under Template Data.