<?php

declare(strict_types=1);

namespace Kickflip\Models;

use Exception;

final class SiteData
{
    private function __construct(
        public string $baseUrl,
        public bool $production,
        /**
         * @var array<NavItem>
         */
        public array $navigation,
        public string $siteName,
        public string $siteDescription,
        public bool $autoExtendMarkdown = true,
    ) {
    }

    /**
     * @param array<void>|array{baseUrl: string, production: bool, siteName: string, siteDescription: string, autoExtendMarkdown: bool} $siteConfig
     * @param array<NavItem> $siteNavigation
     * @return self
     * @throws Exception
     */
    public static function fromConfig(array $siteConfig, array $siteNavigation): self
    {
        if (count($siteConfig) === 0) {
            throw new Exception('Cannot initialize SiteData with empty site config array.');
        }
        if (!isset($siteConfig['baseUrl'], $siteConfig['production'], $siteConfig['siteName'], $siteConfig['siteDescription'])) {
            throw new Exception('Cannot initialize SiteData due to missing required parameter. Must include: baseUrl, production, siteName, siteDescription.');
        }

        return new self(
            baseUrl: $siteConfig['baseUrl'],
            production: $siteConfig['production'],
            navigation: $siteNavigation,
            siteName: $siteConfig['siteName'],
            siteDescription: $siteConfig['siteDescription'],
            autoExtendMarkdown: !isset($siteConfig['autoExtendMarkdown']) ?
                true : $siteConfig['autoExtendMarkdown'],
        );
    }
}
