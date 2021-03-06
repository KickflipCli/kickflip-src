<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\View;

use Kickflip\Models\SiteData;
use Kickflip\View\Engine\BladeMarkdownEngine;
use KickflipMonoTests\Feature\BaseFeatureTestCase;

use function app;

class BladeMarkdownEngineBaseFeatureTest extends BaseFeatureTestCase
{
    public function testCanInstantiateMarkdownEngine()
    {
        $bladeMarkdownEngine = app(BladeMarkdownEngine::class);
        self::assertInstanceOf(BladeMarkdownEngine::class, $bladeMarkdownEngine);
    }

    public function testCanMakeAView()
    {
        $mockSiteData = SiteData::fromConfig([
            'baseUrl' => 'http://example.com',
            'production' => true,
            'siteName' => 'Example Site',
            'siteDescription' => 'This is an example site.',
        ]);
        $mockPageData = $this->getTestPageData(1);

        $data = [
            '__env' => app('view'),
            'app' => app(),
            'site' => $mockSiteData,
            'page' => $mockPageData,
        ];

        $bladeMarkdownEngine = app(BladeMarkdownEngine::class);
        self::assertIsString($bladeMarkdownEngine->get($mockPageData->source->getFullPath(), $data));
        self::assertMatchesHtmlSnapshot($bladeMarkdownEngine->get($mockPageData->source->getFullPath(), $data));
    }

    public function testCanMakeNonExtendedView()
    {
        $mockSiteData = SiteData::fromConfig([
            'baseUrl' => 'http://example.com',
            'production' => true,
            'siteName' => 'Example Site',
            'siteDescription' => 'This is an example site.',
        ]);
        $mockPageData = $this->getTestPageData(7);

        $data = [
            '__env' => app('view'),
            'app' => app(),
            'site' => $mockSiteData,
            'page' => $mockPageData,
        ];

        $bladeMarkdownEngine = app(BladeMarkdownEngine::class);
        self::assertIsString($bladeMarkdownEngine->get($mockPageData->source->getFullPath(), $data));
        self::assertMatchesHtmlSnapshot($bladeMarkdownEngine->get($mockPageData->source->getFullPath(), $data));
    }
}
