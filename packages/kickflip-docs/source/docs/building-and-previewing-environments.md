---
title: Environments
---

Your Kickflip site likely needs to have slightly different configurations in your development and production environments.
Each environment has a config with the respective name in it, so building for `stage` will load `config/config.stage.php`

This can really be used for anything you need, but is most often used for adjusting the base URL.

Kickflip will always load and requires access to the `config/config.php` file.
For any other environment build the respective config will be loaded and merged on top of the global config.

So if your base `config.php` file looks like this:

```php
<?php

return [
    'baseUrl' => 'http://kickflip-docs.test/',
    'production' => false,
    'siteName' => 'Kickflip CLI',
];
```

You can then override the `production` env's variable in the `config.production.php` file like:

```php
<?php

return [
    'baseUrl' => 'https://kickflip.lucidinternets.com/',
    'production' => true,
];
```

Once merged any values in the production file will take precedent and overwrite base values.
The effective merged config would look like:

```php
array:3 [
  "baseUrl" => "https://kickflip.lucidinternets.com/"
  "production" => true
  "siteName" => "Kickflip CLI"
]
```


## Building environment specific files

To build the site for a specific environment just pass the environment name as an argument for the `build` command:

```bash
$ ./vendor/bin/kickflip build production
```

This will generate your site into a new folder called `build_production`, leaving any `build_local` folder untouched.