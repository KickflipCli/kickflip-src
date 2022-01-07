<?php

declare(strict_types=1);

namespace KickflipMonoTests\DocsSite\SiteBuilder;

class LocalSiteBuilderHtmlUrlsTest extends BaseSiteBuilderTest
{
    protected static ?string $buildEnv = 'local';
    protected static bool $prettyUrls = false;
}
