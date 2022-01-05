<?php

declare(strict_types=1);

use League\CommonMark\Extension\Attributes\AttributesExtension;

return [
    /*
     * These extensions should be added to the markdown environment. A valid
     * extension implements League\CommonMark\Extension\ExtensionInterface
     *
     * More info: https://commonmark.thephpleague.com/2.1/extensions/overview/
     */
    'extensions' => [
        AttributesExtension::class,
    ],
];
