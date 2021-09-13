<?php

namespace Kickflip\Models;

class SiteData
{
    public function __construct(
        public string $baseUrl,
        public bool $production,
        public array $navigation,
        public string $siteName,
        public string $siteDescription,
        public ?string $docsearchApiKey = null,
        public ?string $docsearchIndexName = null,
    ) {
    }

    public static function fromConfig(array $siteConfigData, array $siteNavigation): self
    {
        return new self(
            $siteConfigData['baseUrl'],
            $siteConfigData['production'],
            $siteNavigation,
            $siteConfigData['siteName'],
            $siteConfigData['siteDescription'],
            empty($siteConfigData['docsearchApiKey']) || !isset($siteConfigData['docsearchApiKey']) ?
                    null : $siteConfigData['docsearchApiKey'],
            empty($siteConfigData['docsearchIndexName']) || !isset($siteConfigData['docsearchIndexName']) ?
                    null : $siteConfigData['docsearchIndexName'],
        );
    }
}
