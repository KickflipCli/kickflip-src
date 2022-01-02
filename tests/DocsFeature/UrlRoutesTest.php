<?php

declare(strict_types=1);

use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\UrlGenerator;
use Kickflip\SiteBuilder\SourcesLocator;

test('check UrlGenerator session resolver is null', function () {
    /**
     * @var UrlGenerator $url
     */
    $url = app('url');
    expect($url)->reflectCallMethod('getSession')->toBeNull();
});

test('check UrlGenerator key resolver', function () {
    /**
     * @var UrlGenerator $url
     */
    $url = app('url');
    expect($url)
        ->reflectHasProperty('keyResolver');
    $keyResolver = expect($url)
        ->reflectExpectProperty('keyResolver')
        ->toBeCallable()->value;
    expect($keyResolver())->toBeString()->toStartWith('base64:');
    $key1 = $keyResolver();
    $key2 = $keyResolver();
    expect($key1)->toStartWith('base64:')->not()->toEqual($key2);
    expect($key2)->toStartWith('base64:')->not()->toEqual($keyResolver());
});

test('check UrlGenerator rebinds routes', function () {
    /**
     * @var UrlGenerator $url
     */
    $url = app('url');
    /**
     * @var RouteCollection $initialRoutes
     */
    $initialRoutes = clone expect($url)->reflectExpectProperty('routes')->toBeInstanceOf(RouteCollection::class)->value;
    expect($initialRoutes->getRoutes())->toHaveCount(0);
    app(SourcesLocator::class); // This will force routes to be registered...
    $updatedRoutes = expect($url)->reflectExpectProperty('routes')->toBeInstanceOf(RouteCollection::class)->value;
    expect($updatedRoutes->getRoutes())->toHaveCount(count(app(SourcesLocator::class)->getRenderPageList()));
    // Now that the global routes was updated lets override it with the empty clone...
    app()->instance('routes', $initialRoutes);
    $reboundRoutes = expect($url)->reflectExpectProperty('routes')->toBeInstanceOf(RouteCollection::class)->value;
    expect($reboundRoutes->getRoutes())->toHaveCount(0);
});
