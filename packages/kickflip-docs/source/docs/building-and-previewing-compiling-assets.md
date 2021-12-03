---
title: Compiling Assets
tags: ['dude', 'stuff']
---

Just like with Jigsaw (the project Kickflip was inspired by) the default build system is **Laravel Mix**.
If you've ever used Mix in Laravel projects you'll be right at home.
The version of Mix used by Kickflip is `6.0`, staying current with Laravel 8. 

---

## Setup

Getting started is as easy is making sure you have Node.js and NPM.
Then just grab the dependencies before compiling with:

```bash
$ npm install
```

For more details refer to the full [Laravel Mix docs](https://laravel-mix.com/docs/6.0/installation).

## Organizing your assets

Overall organizing your resources is done very similarly to how Laravel would manage them.
The main exception is in where the `public` sources are located at, in Kickflip this is essentially the `sources` folder.

In the end, compiled results of mix will end up within that folder first. And then copied as artifacts into the final `build_*` folders.

### JavaScript and CSS

Organizing your JS and CSS assets in Kickflip is done identically to how you would with Laravel.
This means Mix will look for your assets under the `resources` folder.
Then specific files will end up in their respective folders, for more see Laravel's [Compiling Assets](https://laravel.com/docs/8.x/mix#introduction) page.


Within your core blade templates you can use the `kickflip_mix` helper method as you would use the `mix` helper.

### Blade Templates

The core blade templates will live in your `resources/views` folder just like in Laravel.
You can compose your site using multiple layered Blade views just like you are used to.

### Other static resources

Any other types of static resource files (like images, favicon or other media) should be placed in the `sources` folder.
You should organize it there in the same relative location necessary for the built site.
Accessing resources

## Compiling the assets

To compile the assets run:

```bash
$ npm run {dev/prod}
```

As mentioned, using Webpack via Mix the assets will be compiled into `source/assets/build` directory.
Then Kickflip's `build` will build your site (including any static assets) into your `/build_local` directory.