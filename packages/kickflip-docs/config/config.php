<?php

use BladeUI\Icons\BladeIconsServiceProvider;
use Codeat3\BladeFileIcons\BladeFileIconsServiceProvider;
use Kickflip\RouterNavPlugin\KickflipRouterNavServiceProvider;
use MallardDuck\BladeBoxicons\BladeBoxiconsServiceProvider;
use MallardDuck\BladeEmojiIcons\BladeEmojiIconsServiceProvider;

return [
    'baseUrl' => 'http://kickflip-docs.test/',
    'production' => false,
    'siteName' => 'Kickflip',
    'siteDescription' => 'Static site generation based on Laravel Zero',

    // Composer packages with laravel providers to load
    'providePackages' => [
        KickflipRouterNavServiceProvider::class,
        BladeIconsServiceProvider::class,
        BladeFileIconsServiceProvider::class,
        BladeBoxiconsServiceProvider::class,
        BladeEmojiIconsServiceProvider::class,
    ],

    // Algolia DocSearch credentials
    'docsearchApiKey' => '',
    'docsearchIndexName' => '',
];
