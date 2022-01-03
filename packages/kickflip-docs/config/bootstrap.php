<?php

declare(strict_types=1);

// phpcs:disable
/**
 * This file is loaded at the very end of the KickflipServiceProvider::boot() method.
 * @var KickflipServiceProvider $this
 */
// phpcs:enable

use Illuminate\Support\Facades\Event;
use Kickflip\Events\SiteBuildComplete;
use Kickflip\Models\PageData;
use Kickflip\Providers\KickflipServiceProvider;
use Kickflip\View\Compilers\ComponentTagCompiler;
use KickflipDocs\Listeners\GenerateSitemap;

/*
 * Here is a good place to adjust global defaults, like:
 */
PageData::$defaultExtendsView = 'layouts.documentation';
PageData::$defaultExtendsSection = 'docs_content';

ComponentTagCompiler::$rootNamespace = 'KickflipDocs';

/*
 * You can run custom code at different stages of the build process by
 * listening to the BuildStarted::class, CollectionsBuilt::class, and SiteBuilt::class events.
 *
 * For example:
 * Event::listen({EVENT_CLASS}, function () {
 *     // Your code here
 * });
 */
Event::listen(SiteBuildComplete::class, GenerateSitemap::class);
