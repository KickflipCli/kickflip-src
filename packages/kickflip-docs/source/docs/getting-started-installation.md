---
title: Installation
---
While other installation methods may be supported in the future, for now the best way to get started is to install via Composer!

## Install via Composer
These directions assume your computer already has PHP and Composer installed, if you do not stop and set those up first.
You may create a new Kickflip project by using Composer directly.

After the site has been created, you may use [Laravel's Valet](https://laravel.com/docs/8.x/valet) for local development server:

```bash
composer create-project mallardduck/kickflip example-site

cd example-site

valet link
```

### Getting asset dependencies
Out of the box the starter project uses Laravel's [Mix](https://laravel.com/docs/8.x/mix) for asset compilation, in addition to [tailwindcss](https://tailwindcss.com/docs/text-color).
Before the first site build you must build the Mix assets or you will get an error about `mix()` method missing the manifest.

```bash
npm install
npm run dev
```

### First Build
Once the assets have been built you can run kickflip with:
```bash
./vendor/bin/kickflip build
```