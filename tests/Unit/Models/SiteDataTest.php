<?php

declare(strict_types=1);

namespace KickflipMonoTests\Unit\Models;

use Kickflip\Models\SiteData;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\ReflectionHelpers;
use PHPUnit\Framework\TestCase;
use Throwable;

class SiteDataTest extends TestCase
{
    use DataProviderHelpers;
    use ReflectionHelpers;

    public function testVerifyClassExists()
    {
        self::assertClassExists(SiteData::class);
    }

    public function testItThrowsWhenCreatingEmptySiteData()
    {
        $this->expectException(Throwable::class);
        $this->expectExceptionMessage('Cannot initialize SiteData with empty site config array.');
        SiteData::fromConfig([]);
    }

    /**
     * @dataProvider invalidSiteConfigProviders
     */
    public function testItThrowsWhenMissingRequiredParams(array $siteConfig)
    {
        $this->expectException(Throwable::class);
        $this->expectExceptionMessage('Cannot initialize SiteData due to missing required parameter. Must include: baseUrl, production, siteName, siteDescription.');
        SiteData::fromConfig($siteConfig);
    }

    public function invalidSiteConfigProviders()
    {
        return $this->autoAddDataProviderKeys([
            [['yeet']],
            [['baseUrl', 'production', 'siteName', 'siteDescription']],
            [['baseUrl', 'production' => '', 'siteName' => '', 'siteDescription' => '']],
            [['baseUrl' => '', 'production', 'siteName' => '', 'siteDescription' => '']],
            [['baseUrl' => '', 'production' => '', 'siteName', 'siteDescription' => '']],
            [['baseUrl' => '', 'production' => '', 'siteName' => '', 'siteDescription']],
        ]);
    }

    public function testItCanCreateAValidSiteData()
    {
        $siteData = SiteData::fromConfig([
            'baseUrl' => 'http://example.com',
            'production' => true,
            'siteName' => 'Example Site',
            'siteDescription' => 'This is an example site.',
        ]);
        self::assertInstanceOf(SiteData::class, $siteData);
        self::assertHasProperty($siteData, 'baseUrl', 'http://example.com');
        self::assertHasProperty($siteData, 'production', true);
        self::assertHasProperty($siteData, 'siteName', 'Example Site');
        self::assertHasProperty($siteData, 'siteDescription', 'This is an example site.');
    }
}
