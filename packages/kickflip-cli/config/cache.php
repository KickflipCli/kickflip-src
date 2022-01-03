<?php

declare(strict_types=1);

return [
    'default' => 'array',

    'stores' => [
        'array' => [
            'driver' => 'array',
        ],
        'file' => [
            'driver' => 'file',
            'path' => storage_path('cache'),
        ],
    ],
];
