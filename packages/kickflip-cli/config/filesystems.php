<?php

return [
    'default' => 'local',
    'disks' => [
        'app' => [
            'driver' => 'local',
            'root' => getenv("HOME"). DIRECTORY_SEPARATOR . ".kickflip-cli",
        ],
        'local' => [
            'driver' => 'local',
            'root' => \Phar::running() ? getcwd() : storage_path(),
        ],
    ],
];
