---
title: Directory Structure
---

Kickflip is a Laravel-based open-source framework for creating static content based websites.
It's great for building basic portfolio sites, simple blogs, or documentation sites.

# Introduction
The most basic Kickflip project structure will be detailed here.

![The project directory structure]({{ kickflip_asset_url('img/directory-structure-example.png') }} "An example of the project structure for kickflip.")

# The Root Directory
## The App Directory
Ultimately this directory will usually be pretty minimal in a site based on Kickflip.
After all, sites built with a static site generate shouldn't be too complex in an "app" sense.

That said, you can use this folder just like you would with any other Laravel based site.
Primarily you will be concerned with using the `Listeners` and `View` folders in Kickflip.
Read more about the App folder on the Laravel [Directory Structure](https://laravel.com/docs/master/structure#the-app-directory) page.

## The Config Directory
The `config` directory, as the name implies, contains all of your application's configuration files. It's a great idea to read through all of these files and familiarize yourself with all of the options available to you.

## The Resources Directory
The `resources` directory contains your views as well as your raw, un-compiled assets such as CSS or JavaScript. This directory also houses all of your language files.

## The Source Directory
The `source` directory is where you will create the content of your website.
Any files in this folder will either be: a) copied directly to the output, or b) parsed and rendered before saving to output.
