<?php

use Illuminate\View\View;
use Kickflip\Models\SiteData;
use KickflipMonoTests\Mocks\MarkdownHelpersMock;
use Spatie\LaravelMarkdown\MarkdownRenderer as BaseMarkdownRenderer;

it('can determine if autoExtend is enabled', function () {
    $mockSiteData = SiteData::fromConfig([
        'baseUrl' => 'http://example.com',
        'production' => true,
        'siteName' => 'Example Site',
        'siteDescription' => 'This is an example site.',
    ]);
    $mockPageData = getTestPageData();

    $markdownHelpers = new MarkdownHelpersMock;
    expect($markdownHelpers->isAutoExtendEnabled($mockSiteData, $mockPageData))
        ->toBeTrue();
});

it('can determine if page has Extend enabled', function () {
    $mockSiteData = SiteData::fromConfig([
        'baseUrl' => 'http://example.com',
        'production' => true,
        'siteName' => 'Example Site',
        'siteDescription' => 'This is an example site.',
    ]);
    $mockPageData = getTestPageData();
    $renderedPageMarkdown = app(BaseMarkdownRenderer::class)
        ->convertToHtml(
            file_get_contents($mockPageData->source->getFullPath())
        );

    $markdownHelpers = new MarkdownHelpersMock;
    expect($markdownHelpers->isPageExtendEnabled($mockPageData, $renderedPageMarkdown))
        ->toBeTrue();
});

it('can prepare extended rendered for markdown', function (int $pageId, string $expectedSection, string $expectedExtends) {
    $mockSiteData = SiteData::fromConfig([
        'baseUrl' => 'http://example.com',
        'production' => true,
        'siteName' => 'Example Site',
        'siteDescription' => 'This is an example site.',
    ]);
    $mockPageData = getTestPageData($pageId);
    $renderedPageMarkdown = app(BaseMarkdownRenderer::class)
        ->convertToHtml(
            file_get_contents($mockPageData->source->getFullPath())
        );
    $markdownHelpers = new MarkdownHelpersMock;
    $preparedExtendedRender = $markdownHelpers->prepareExtendedRender($mockPageData, $renderedPageMarkdown);
    expect($preparedExtendedRender)
        ->toBeArray()->toHaveCount(3);
    expect($preparedExtendedRender[0])
        ->toBeString()->toBe($expectedSection);
    expect($preparedExtendedRender[1])
        ->toBeString();
    expect($preparedExtendedRender[2])
        ->toBeString()->toBe($expectedExtends);
})->with([
    [0, 'content', 'layouts.master'],
    [1, 'postContent', 'layouts.post'],
    [5, 'body', 'layouts.master'],
]);

it('throws an exception with non-extended PageData', function () {
    $mockSiteData = SiteData::fromConfig([
        'baseUrl' => 'http://example.com',
        'production' => true,
        'siteName' => 'Example Site',
        'siteDescription' => 'This is an example site.',
    ]);
    $mockPageData = getTestPageData(6);
    $renderedPageMarkdown = app(BaseMarkdownRenderer::class)
        ->convertToHtml(
            file_get_contents($mockPageData->source->getFullPath())
        );
    $markdownHelpers = new MarkdownHelpersMock;
    $this->expectError();
    $markdownHelpers->prepareExtendedRender($mockPageData, $renderedPageMarkdown);
});

it('can make a view', function () {
    $mockSiteData = SiteData::fromConfig([
        'baseUrl' => 'http://example.com',
        'production' => true,
        'siteName' => 'Example Site',
        'siteDescription' => 'This is an example site.',
    ]);
    $mockPageData = getTestPageData();
    $renderedPageMarkdown = app(BaseMarkdownRenderer::class)
        ->convertToHtml(
            file_get_contents($mockPageData->source->getFullPath())
        );

    $markdownHelpers = new MarkdownHelpersMock;
    $preparedExtendedRender = $markdownHelpers->makeView([
        '__env' => app('view'),
        'app' => app(),
        'site' => $mockSiteData,
        'page' => $mockPageData,
    ], $renderedPageMarkdown);
    expect($preparedExtendedRender)
        ->toBeInstanceOf(View::class);
    expect($preparedExtendedRender->render())->toBeString();
});
