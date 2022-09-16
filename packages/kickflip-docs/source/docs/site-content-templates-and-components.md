---
title: Blade Templates & Components
---

Hands down, the best part about Kickflip is that our templates work exactly like Laravel core. That mean's if you're familiar with Blade you'll be right at home.

You'll find:
* your templates exist in `resources/views`,
* you can drop in existing Blade based site templates<sup>1</sup>,
* you can make rich interactions using client-side JS like [Alpine.js](https://alpinejs.dev/), and
* you can even create and use Blade components (all types)!

<sup>1 = Assuming the template can logically be rendered as static - no Livewire sorry.</sup>

## Getting Started

Ultimately, if you are unfamiliar with Laravel's Blade templates these docs won't cover enough. You're really best off reading the [excellent docs on Blade from Laravel](https://laravel.com/docs/9.x/blade). Our docs will simply cover areas where static compliling and Blade may not play nice.

### Forms

Using HTML web forms with Kickflip is absolutely possible - however due to it being a static platform, the native Laravel form collection does not work. Simply, without the Laravel app living on a server you cannot post Form data to it.

 You can still create and/or embed froms to your Kickflip sites though. You just have to keep some things in mind; for instance, anything related to CSRF/XSRF/etc that Laravel provides will not be useful.

You can instead create forms on your pages and use a webfrom SaaS like: HubSpot, Typeform, most any CRM software, etc. Alternatively, some static content hosting platforms (like Netlify, who host these docs) do support forms.

## Blade Templates vs Blade Source Content

The keen-eyed reading these docs may have noticed you can use Blade as a source of content too. So on top of easy to use Markdown content sources you can have more complex Blade based content pages. This feature can provide more control for rendering more complex static pages.

Interestingly, all of the renderable content Kickflip finds in `sources` is treated as a view internally. That's to say, once Kickflip has a list of our source files they are rendered by directly calling Laravel's `view()` function. Once called to render, Laravel identifies the source type and they are rendered by one of our custom View Engine classes.