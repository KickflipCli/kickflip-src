<?php

declare(strict_types=1);

namespace KickflipMonoTests\Unit\Enums;

use Kickflip\Collection\SortOption;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\ReflectionHelpers;
use PHPUnit\Framework\TestCase;

class SortOptionEnumsTest extends TestCase
{
    use DataProviderHelpers;
    use ReflectionHelpers;

    /**
     * @dataProvider sortOptionProvider
     */
    public function testItCanConstructVerbosityFlag(SortOption $input, string $expected)
    {
        self::assertInstanceOf(SortOption::class, $input);
        self::assertIsScalar($input->value);
        self::assertIsString($input->value);
        self::assertEquals($expected, $input->value);
    }

    /**
     * @return array<array-key, array<array-key, SortOption|string>>
     */
    public function sortOptionProvider(): array
    {
        return $this->autoAddDataProviderKeys([
            [SortOption::custom(), 'custom'],
        ]);
    }

    public function testItCanVerifyEnumValues(): void
    {
        self::assertIsArray(self::reflectionCallMethod(SortOption::class, 'values'));
    }

    public function testRegularOptionFilters(): void
    {
        self::assertIsCallable(SortOption::name()->toFilter());
        self::assertIsCallable(SortOption::relativePath()->toFilter());
        self::assertIsCallable(SortOption::relativeDirectoryPath()->toFilter());
    }

    public function testSortOptionValues(): void
    {
        $values = SortOption::toValues();
        self::assertIsArray($values);
        self::assertCount(4, $values);
        self::assertContains('custom', $values);
        self::assertContains('name', $values);
        self::assertContains('relativePath', $values);
        self::assertContains('relativeDirectoryPath', $values);
    }

    public function testSortOptionLabels(): void
    {
        $labels = SortOption::toLabels();
        self::assertIsArray($labels);
        self::assertCount(4, $labels);
        self::assertContains('custom', $labels);
        self::assertContains('name', $labels);
        self::assertContains('relativePath', $labels);
        self::assertContains('relativeDirectoryPath', $labels);
    }

    public function testBackedEnumExample(): void
    {
        $backedEnum = SortOption::custom('route');
        self::assertInstanceOf(SortOption::class, $backedEnum);
        self::assertEquals('custom', $backedEnum->value);
        self::assertEquals('route', $backedEnum->backedValue);
        self::assertIsCallable($backedEnum->toFilter());
    }
}
