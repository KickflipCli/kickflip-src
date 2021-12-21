# How to Contribute

Contributions are always more than welcomed!
However, they may not always be accepted if the project cannot support the feature.

You can contribute to [kickflip-src](https://github.com/mallardduck/kickflip-src) repository.

## Preparing Local Environment

To use Kickflip CLI you will need PHP 8.0 and composer. Once the repo is pulled you must run `composer install`.
Then, to verify the changes you're proposing don't break anything run: `composer test`.

To contribute to Kickflip CLI you do not need to set up the Docs site.
However, it can help with testing and debugging as it provides more extensive scenarios for Markdown rendering.
The time the docs site are even used as source for testing the build command.

    $ cd ~/GitProjects/kickflip-monorepo/packages/kickflip-docs
    $ ../kickflip-cli/bin/kickflip build 

Note: Adjust the directory for where you cloned the repo.

While Kickflip is meant to generate static HTML file sites, using [Laravel Valet](https://laravel.com/docs/8.x/valet) can help provide a basic webserver for viewing.