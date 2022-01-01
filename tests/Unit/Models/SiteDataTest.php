<?php

use Kickflip\Models\SiteData;

it('throws exception when creating empty SiteData', function () {
    SiteData::fromConfig([]);
})->throws(\Exception::class, 'Cannot initialize SiteData with empty site config array.');

it('throws exception when creating SiteData with missing required param', function ($siteConfig) {
    SiteData::fromConfig($siteConfig);
})->throws(\Exception::class, 'Cannot initialize SiteData due to missing required parameter. Must include: baseUrl, production, siteName, siteDescription.')->with([
    [['yeet']],
    [['baseUrl', 'production', 'siteName', 'siteDescription']],
    [['baseUrl', 'production' => '', 'siteName' => '', 'siteDescription' => '']],
    [['baseUrl' => '', 'production', 'siteName' => '', 'siteDescription' => '']],
    [['baseUrl' => '', 'production' => '', 'siteName', 'siteDescription' => '']],
    [['baseUrl' => '', 'production' => '', 'siteName' => '', 'siteDescription']],
]);

it('can create a valid SiteData with proper parameters', function () {
    expect(SiteData::fromConfig([
        'baseUrl' => 'http://example.com',
        'production' => true,
        'siteName' => 'Example Site',
        'siteDescription' => 'This is an example site.',
    ]))->toBeInstanceOf(SiteData::class)
        ->toHaveProperty('baseUrl', 'http://example.com')
        ->toHaveProperty('production', true)
        ->toHaveProperty('siteName', 'Example Site')
        ->toHaveProperty('siteDescription', 'This is an example site.')
    ;
});
