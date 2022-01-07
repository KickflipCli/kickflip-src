<?php

declare(strict_types=1);

/**
 * This file is loaded at the very end of the KickflipServiceProvider::boot() method.
 *
 * @var KickflipServiceProvider $this
 */

use Illuminate\Support\Facades\Event;
use Kickflip\Events\SiteBuildComplete;
use Kickflip\Listeners\GenerateSitemap;
use Kickflip\Providers\KickflipServiceProvider;

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
