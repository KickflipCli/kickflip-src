<?php

use MallardDuck\BladeEmojiIcons\BladeEmojiIconsServiceProvider;

return [
    'baseUrl' => '',
    'production' => false,
    'siteName' => 'Kickflip',
    'siteDescription' => 'The static site generation based on Laravel Zero',

    // Composer packages with laravel providers to load
    'providePackages' => [
        BladeEmojiIconsServiceProvider::class,
    ],
];