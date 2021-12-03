---
title: Environments
---

Your kickflip sites likely need to have slightly different configurations in your development and production environments.
This can really be used for anything you need, but is most often used for adjusting the base URL.

The way Kickflip loads the build configuration is by first loading the global `config.php` file.
When building the site this will be the main config used no matter what environment is being built.
For Staging or Production builds the respective config will be loaded on top.

So if your base `config.php` file looks like this:

```php
<?php

return [
    'baseUrl' => 'http://kickflip-docs.test/',
    'production' => false,
    'siteName' => 'Kickflip',
];
```

You can then override the production variable in the `config.production.php` file like:

```php
<?php

return [
    'baseUrl' => 'https://kickflip.lucidinternets.com/',
    'production' => true,
];
```

Once merged any values in the production file will take precedent and overwrite base values.

## Building environment specific files

To build the site for a specific environment just pass the environment name as an argument for the `build` command:

```bash
$ ./vendor/bin/kickflip build production
```

This will generate your site into a new folder called `build_production`, leaving any `build_local` folder untouched.