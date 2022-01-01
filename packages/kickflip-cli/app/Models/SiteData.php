<?php

declare(strict_types=1);

namespace Kickflip\Models;

use Exception;

final class SiteData
{
    private function __construct(
        public string $baseUrl,
        public bool $production,
        public string $siteName,
        public string $siteDescription,
        public bool $autoExtendMarkdown = true,
    ) {
    }

    /**
     * @param array<void>|array{baseUrl: string, production: bool, siteName: string, siteDescription: string, autoExtendMarkdown: bool} $siteConfig
     * @return self
     * @throws Exception
     */
    public static function fromConfig(array $siteConfig): self
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
            siteName: $siteConfig['siteName'],
            siteDescription: $siteConfig['siteDescription'],
            autoExtendMarkdown: $siteConfig['autoExtendMarkdown'] ?? true,
        );
    }
}
