<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\Models;

use Kickflip\KickflipHelper;
use Kickflip\Models\PageData;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\Feature\BaseFeatureTestCase;
use KickflipMonoTests\ReflectionHelpers;
use Throwable;

use function dirname;

class PageDataBaseFeatureTest extends BaseFeatureTestCase
{
    use DataProviderHelpers;
    use ReflectionHelpers;

    public function testThrowsUsingNewOnPageData()
    {
        $this->expectError();
        // phpcs:ignore
        $this->expectErrorMessage('Call to private Kickflip\Models\PageData::__construct() from scope ' . self::class);
        /**
         * @psalm-suppress InaccessibleMethod
         * @psalm-suppress TooFewArguments
         * @phpstan-ignore-next-line
         */
        new PageData();
    }

    public function testCanInstantiateWithSourcePageMeta()
    {
        $pageData = $this->getTestPageData();
        self::assertInstanceOf(PageData::class, $pageData);

        // Temporary set Pretty URLs to false..
        KickflipHelper::config()->set('prettyUrls', false);
        self::assertIsString($pageData->getUrl());
        self::assertEquals('/basic.html', $pageData->getUrl());

        // Change Pretty URLs back
        KickflipHelper::config()->set('prettyUrls', true);
        self::assertIsString($pageData->getUrl());
        self::assertEquals('/basic', $pageData->getUrl());

        // Temporary set Pretty URLs to false...
        KickflipHelper::config()->set('prettyUrls', false);
        self::assertIsString($pageData->getOutputPath());
        self::assertEquals(
            dirname(__FILE__, 4) . self::agnosticPath('/packages/kickflip/build_{env}/basic.html'),
            $pageData->getOutputPath(),
        );

        // Change Pretty URLs back
        KickflipHelper::config()->set('prettyUrls', true);
        self::assertIsString($pageData->getOutputPath());
        self::assertEquals(
            dirname(__FILE__, 4) . self::agnosticPath('/packages/kickflip/build_{env}/basic/index.html'),
            $pageData->getOutputPath(),
        );

        // Check Extends values
        self::assertIsString($pageData->getExtendsView());
        self::assertEquals('layouts.default', $pageData->getExtendsView());
        self::assertIsString($pageData->getExtendsSection());
        self::assertEquals('content', $pageData->getExtendsSection());

        // Get title
        self::assertIsString($pageData->getTitleId());
        self::assertEquals('basic', $pageData->getTitleId());

        /**
         * check tags
         *
         * @phpstan-ignore-next-line
         */
        self::assertIsArray($pageData->tags);
        self::assertEquals([
            'test',
            'data',
        ], $pageData->tags);
    }

    public function testExpectsExceptionForUndefinedProp()
    {
        $pageData = $this->getTestPageData();

        $this->expectException(Throwable::class);
        $this->expectExceptionMessage(
            'Undefined property via __get(): nana in ' .
            dirname(__FILE__, 4) .
            self::agnosticPath('/tests/Feature/Models/PageDataBaseFeatureTest.php'),
        );
        /**
         * @phpstan-ignore-next-line
         */
        $pageData->nana;
    }
}
