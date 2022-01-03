<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\View;

use Kickflip\Models\SiteData;
use Kickflip\View\Engine\MarkdownEngine;
use KickflipMonoTests\TestCase;

use function app;

class MarkdownEngineTest extends TestCase
{
    public function testCanInstantiateMarkdownEngine()
    {
        $markdownEngine = app(MarkdownEngine::class);
        self::assertInstanceOf(MarkdownEngine::class, $markdownEngine);
    }

    public function testCanMakeAView()
    {
        $mockSiteData = SiteData::fromConfig([
            'baseUrl' => 'http://example.com',
            'production' => true,
            'siteName' => 'Example Site',
            'siteDescription' => 'This is an example site.',
        ]);
        $mockPageData = $this->getTestPageData(0);

        $data = [
            '__env' => app('view'),
            'app' => app(),
            'site' => $mockSiteData,
            'page' => $mockPageData,
        ];

        $markdownEngine = app(MarkdownEngine::class);
        self::assertIsString($markdownEngine->get($mockPageData->source->getFullPath(), $data));
    }

    public function testCanMakeNonExtendedView()
    {
        $mockSiteData = SiteData::fromConfig([
            'baseUrl' => 'http://example.com',
            'production' => true,
            'siteName' => 'Example Site',
            'siteDescription' => 'This is an example site.',
        ]);
        $mockPageData = $this->getTestPageData(6);

        $data = [
            '__env' => app('view'),
            'app' => app(),
            'site' => $mockSiteData,
            'page' => $mockPageData,
        ];

        $markdownEngine = app(MarkdownEngine::class);
        self::assertIsString($markdownEngine->get($mockPageData->source->getFullPath(), $data));
    }
}
