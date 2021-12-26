<?php

/**
 * This file is loaded at the very end of the KickflipServiceProvider::boot() method.
 *
 * @var \Kickflip\Providers\KickflipServiceProvider $this
 */

use Kickflip\View\Compilers\ComponentTagCompiler;
use KickflipDocs\Listeners\GenerateSitemap;
use Illuminate\Support\Facades\Event;
use Kickflip\Events\SiteBuildComplete;

/**
 * Here is a good place to adjust global defaults, like:
 */
\Kickflip\Models\PageData::$defaultExtendsView = 'layouts.documentation';
\Kickflip\Models\PageData::$defaultExtendsSection = 'docs_content';

ComponentTagCompiler::$rootNamespace = 'KickflipDocs';

/**
 * You can run custom code at different stages of the build process by
 * listening to the BuildStarted::class, CollectionsBuilt::class, and SiteBuilt::class events.
 *
 * For example:
 * Event::listen({EVENT_CLASS}, function () {
 *     // Your code here
 * });
 */
Event::listen(SiteBuildComplete::class, GenerateSitemap::class);
