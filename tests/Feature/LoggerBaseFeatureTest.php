<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature;

use Illuminate\Config\Repository;
use Illuminate\Support\Collection;
use Kickflip\Logger;

use ReflectionClass;
use function app;

class LoggerBaseFeatureTest extends BaseFeatureTestCase
{

    /**
     * @var array{0: \ReflectionClass<Repository>, 1: \ReflectionProperty}
     */
    private array $timingReflection;

    public function setUp(): void
    {
        parent::setUp();
        Collection::macro('filterTimings', [static::class, 'stripTimeValues']);
    }

    public static function stripTimeValues($input)
    {
        if (is_float($input)) {
            return '<DUMMY VALUE>';
        }

        if (is_array($input)) {
            $subItems = collect($input);
            return $subItems->map([static::class, 'stripTimeValues'])->toArray();
        }

        return $input;
    }

    public function testLoggerTimingWorksCorrectly()
    {
        $timingsRepo = app('kickflipTimings');
        self::assertInstanceOf(Repository::class, $timingsRepo);
        self::assertMatchesObjectSnapshot(Collection::filterTimings($timingsRepo->all()));

        // Make Step 1
        Logger::timing('NotStatic::stepOne');
        self::assertMatchesObjectSnapshot(Collection::filterTimings($timingsRepo->all()));
        self::assertIsArray($timingsRepo->get('NotStatic'));
        self::assertCount(1, $timingsRepo->get('NotStatic'));

        // Make step 2
        Logger::timing('NotStatic::stepTwo', 'Static');
        self::assertMatchesObjectSnapshot(Collection::filterTimings($timingsRepo->all()));
        self::assertIsArray($timingsRepo->get('NotStatic'));
        self::assertCount(2, $timingsRepo->get('NotStatic'));

        // Make step 3
        Logger::timing('Static::stepThree');
        self::assertMatchesObjectSnapshot(Collection::filterTimings($timingsRepo->all()));
        self::assertIsArray($timingsRepo->get('Static'));
        self::assertCount(1, $timingsRepo->get('Static'));
    }
}
