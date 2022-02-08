<?php

declare(strict_types=1);

use Kickflip\Collection\CollectionConfig;
use Kickflip\Collection\InverseSortOption;
use Kickflip\Collection\SortOption;
use Kickflip\Models\ExtendsInfo;
use MallardDuck\BladeEmojiIcons\BladeEmojiIconsServiceProvider;

return [
    'baseUrl' => 'http://kickflip.test',
    'production' => false,
    'siteName' => 'Kickflip',
    'siteDescription' => 'The static site generation based on Laravel Zero',

    'collections' => [
        CollectionConfig::make(
            'zombies',
            extends: ExtendsInfo::make('layouts.blog', 'content'),
            path: 'posts',
            sort: [SortOption::custom('index')]
        ),
        CollectionConfig::make(
            'posts',
            url: 'blog',
            extends: ExtendsInfo::make('layouts.blog', 'content'),
            sort: [InverseSortOption::custom('index')],
        ),
    ],

    // Composer packages with laravel providers to load
    'providePackages' => [
        BladeEmojiIconsServiceProvider::class,
    ],
];
