<?php

declare(strict_types=1);

use Kickflip\Events\SiteBuildComplete;
use Kickflip\Events\SiteBuildStarted;

it('can verify Event classes exist', function () {
    expect(new SiteBuildStarted())->toBeInstanceOf(SiteBuildStarted::class);
    expect(new SiteBuildComplete())->toBeInstanceOf(SiteBuildComplete::class);
});
