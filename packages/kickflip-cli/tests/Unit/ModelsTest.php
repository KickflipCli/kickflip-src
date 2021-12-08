<?php

use Illuminate\Support\Str;
use Kickflip\KickflipHelper;
use Kickflip\Models\NavItem;
use Kickflip\Models\PageData;
use Kickflip\Models\SiteData;
use Kickflip\Models\SourcePageMetaData;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

it('throws exception when creating empty SiteData', function () {
    SiteData::fromConfig([], []);
})->throws(\Exception::class, 'Cannot initialize SiteData with empty site config array.');

test('can instantiate a SourcePageMetaData', function (SplFileInfo $splFileInfo) {
    $sourcePageMetaData = SourcePageMetaData::fromSplFileInfo($splFileInfo);
    expect($sourcePageMetaData)
        ->toBeInstanceOf(SourcePageMetaData::class);
    expect($sourcePageMetaData->getName())
        ->toBeString()
        ->toBe($splFileInfo->getFilenameWithoutExtension());
    expect($sourcePageMetaData->getFilename())
        ->toBeString()
        ->toBe($splFileInfo->getFilename());
    expect($sourcePageMetaData->getExtension())
        ->toBeString()
        ->toBe($splFileInfo->getExtension());
    expect($sourcePageMetaData->getFullPath())
        ->toBeString()
        ->toBe($splFileInfo->getRealPath());
    expect($sourcePageMetaData->getMimeExtension())
        ->toBeString()
        ->toBe((string) Str::of($splFileInfo->getFilename())->after('.'));
})->with(iterator_to_array(
    Finder::create()->files()->ignoreDotFiles(true)->in(dirname(__DIR__) . '/mock-app/source/')->sortByName(),
    false
));

test('it can create a PageData instance', function () {
    $finder = Finder::create()->files()
        ->ignoreDotFiles(true)
        ->in(dirname(__DIR__) . '/mock-app/source/')
        ->sortByName();
    $fileInfo = iterator_to_array($finder, false)[0];
    $pageMetaData = SourcePageMetaData::fromSplFileInfo($fileInfo);

    $frontMatterData = KickflipHelper::getFrontMatterParser()
            ->parse(file_get_contents($pageMetaData->getFullPath()))
            ->getFrontMatter() ?? [];
    $pageData = PageData::make(
        $pageMetaData,
        $frontMatterData,
    );
    expect($pageData)
        ->toBeInstanceOf(PageData::class);
    expect($pageData->getUrl())
        ->toBeString()
        ->toBe('basic.html');
    expect($pageData->getUrl(true))
        ->toBeString()
        ->toBe('basic');
    expect($pageData->getExtendsView())
        ->toBeString()
        ->toBe('layouts.master');
    expect($pageData->getExtendsSection())
        ->toBeString()
        ->toBe('content');
    expect($pageData->getTitleId())
        ->toBeString()
        ->toBe('basic');
    expect($pageData->tags)
        ->toBeArray()
        ->toHaveCount(2);
    expect(fn() => $pageData->booger)->toThrow(\Exception::class);
});

test('a basic NavItem can be created', function($title, $url) {
    $navItem = NavItem::make($title, $url);
    expect($navItem->getLabel())->toBeString()->toBe($title);
    expect($navItem->title)->toBeString()->toBe($title);
    expect($navItem->hasUrl())->toBeTrue();
    expect($navItem->getUrl())->toBeString()->toBe($url);
    expect($navItem->url)->toBeString()->toBe($url);
    expect($navItem->hasChildren())->toBeFalse();
})->with([
    ['Basic Page', '/basic'],
    ['Another Page', '/another-page'],
]);

test('an advanced NavItem can be created', function() {
    $basicPage = NavItem::make('Basic Page', '/basic');
    $basicPage->setChildren([
        NavItem::make('Another Page', '/another-page')
    ]);
    expect($basicPage->hasChildren())->toBeTrue();
});
