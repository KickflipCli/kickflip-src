<?php

use Illuminate\Support\Facades\File;
use Kickflip\KickflipHelper;
use Kickflip\Models\PageData;
use Kickflip\Models\SourcePageMetaData;

it('throws trying to instantiate PageData with new', function () {
    new PageData();
})->throws(\Error::class, 'Call to private Kickflip\Models\PageData::__construct() from scope P\Tests\Feature\Models\PageDataTest');

it('can instantiate PageData with from SourcePageMetaData', function () {
    // Fetch a single Symfony SplFileInfo object
    $splFileInfo = File::files(__DIR__ . '/../../sources/')[0];
    // Create a SourcePageMetaData object
    $sourcePageMetaData = SourcePageMetaData::fromSplFileInfo($splFileInfo);
    // Parse out the frontmatter page meta data
    $frontMatterData = KickflipHelper::getFrontMatterParser()
            ->parse(file_get_contents($sourcePageMetaData->getFullPath()))
            ->getFrontMatter() ?? [];
    // Create a PageData object
    $pageData = PageData::make($sourcePageMetaData, $frontMatterData);

    // Test the PageData object
    expect($pageData)->toBeInstanceOf(PageData::class);
    expect($pageData->getUrl())->toBeString()->toBe('basic.html');
    expect($pageData->getUrl(true))->toBeString()->toBe('basic');
    expect($pageData->getOutputPath())->toBeString()->toBe('/Users/danpock/GitProjects/kickflip-monorepo/packages/kickflip-docs/build_{env}/basic');
    expect($pageData->getExtendsView())->toBeString()->toBe('layouts.master');
    expect($pageData->getExtendsSection())->toBeString()->toBe('content');
    expect($pageData->getTitleId())->toBeString()->toBe('basic');
    expect($pageData->tags)->toBeArray()->toBe([
        'test',
        'data',
    ]);

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Undefined property via __get(): nana in /Users/danpock/GitProjects/kickflip-monorepo/tests/Feature/Models/PageDataTest.php on line 39');
    $pageData->nana;
});
