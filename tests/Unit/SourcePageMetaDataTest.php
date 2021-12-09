<?php

use Kickflip\Models\SourcePageMetaData;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

test('SourcePageMetaData class exists', function () {
    expect(SourcePageMetaData::class)->toBeString();
    $this->assertTrue(class_exists(SourcePageMetaData::class));
});

it('throws an error instantiating SourcePageMetaData with new', function () {
    new SourcePageMetaData('', '', '', '');
})->throws(\Error::class);

it('can getType from SourcePageMetaData instances', function (SplFileInfo $splFileInfo) {
    $sourcePageMetaData = SourcePageMetaData::fromSplFileInfo($splFileInfo);
    expect($sourcePageMetaData)->toHaveProperties(['viewName', 'implicitExtension']);
    expect($sourcePageMetaData->getName())->toBeString();
    expect($sourcePageMetaData->getFilename())->toBeString();
    expect($sourcePageMetaData->getFullPath())->toBeString();
    expect($sourcePageMetaData->getExtension())->toBeString();
    expect($sourcePageMetaData->getMimeExtension())->toBeString();
    expect($sourcePageMetaData->getType())->toBeString();
})->with(
    Finder::create()
    ->files()
    ->in(dirname(__DIR__) . '/sources')
    ->ignoreDotFiles(true)
    ->getIterator()
);
