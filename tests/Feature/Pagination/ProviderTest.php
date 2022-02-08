<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\Pagination;

use Kickflip\Providers\CustomPaginatorServiceProvider;
use KickflipMonoTests\Feature\BaseFeatureTestCase;

use function class_exists;
use function in_array;

class ProviderTest extends BaseFeatureTestCase
{
    public function testProviderProvides(): void
    {
        self::assertTrue(class_exists(CustomPaginatorServiceProvider::class));
        $customProvider = new CustomPaginatorServiceProvider($this->app);
        self::assertIsArray($customProvider->provides());
        self::assertTrue(in_array('translator', $customProvider->provides()));
        self::assertTrue(in_array('translation.loader', $customProvider->provides()));
    }
}
