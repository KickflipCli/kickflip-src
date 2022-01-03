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

    public function testVerifyClassExists(): void
    {
        self::assertClassExists(SiteData::class);
    }

    public function testItThrowsWhenCreatingEmptySiteData(): void
    {
        $this->expectException(Throwable::class);
        $this->expectExceptionMessage('Cannot initialize SiteData with empty site config array.');
        SiteData::fromConfig([]);
    }

    /**
     * @param array<array-key, array<array-key, string[]>> $siteConfig
     *
     * @dataProvider invalidSiteConfigProviders
     */
    public function testItThrowsWhenMissingRequiredParams(array $siteConfig): void
    {
        $this->expectException(Throwable::class);
        // phpcs:ignore
        $this->expectExceptionMessage('Cannot initialize SiteData due to missing required parameter. Must include: baseUrl, production, siteName, siteDescription.');
        SiteData::fromConfig($siteConfig);
    }

    /**
     * @return array<array-key, array<array-key, string[]>>
     */
    public function invalidSiteConfigProviders(): array
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

    public function testItCanCreateAValidSiteData(): void
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
