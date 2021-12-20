<?php


use Kickflip\Events\SiteBuildStarted;
use Kickflip\Events\SiteBuildComplete;

it('can verify Event classes exist', function () {
    expect(new SiteBuildStarted())->toBeInstanceOf(SiteBuildStarted::class);
    expect(new SiteBuildComplete())->toBeInstanceOf(SiteBuildComplete::class);
});
