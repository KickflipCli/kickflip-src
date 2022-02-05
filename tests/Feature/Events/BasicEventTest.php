<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\Events;

use Kickflip\Events\BeforeConfigurationLoads;
use Kickflip\Events\PageDataCreated;
use Kickflip\Events\SiteBuildComplete;
use Kickflip\Events\SiteBuildStarted;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\TestCase;

class BasicEventTest extends TestCase
{
    use DataProviderHelpers;

    public function testVerifyBeforeConfigurationLoadsExists()
    {
        self::assertInstanceOf(BeforeConfigurationLoads::class, new BeforeConfigurationLoads());
    }

    public function testVerifyPageDataCreatedExists()
    {
        self::assertInstanceOf(PageDataCreated::class, new PageDataCreated($this->getTestPageData(2)));
    }

    public function testVerifySiteBuildStartedExists()
    {
        self::assertInstanceOf(SiteBuildStarted::class, new SiteBuildStarted());
    }

    public function testVerifySiteBuildCompleteExists()
    {
        self::assertInstanceOf(SiteBuildComplete::class, new SiteBuildComplete());
    }
}
