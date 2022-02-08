<?php

declare(strict_types=1);

namespace KickflipMonoTests\Unit\Enums;

use Kickflip\Collection\InverseSortOption;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\ReflectionHelpers;
use PHPUnit\Framework\TestCase;

class InverseSortOptionEnumsTest extends TestCase
{
    use DataProviderHelpers;
    use ReflectionHelpers;

    /**
     * @dataProvider sortOptionProvider
     */
    public function testItCanConstructVerbosityFlag(InverseSortOption $input, string $expected)
    {
        self::assertInstanceOf(InverseSortOption::class, $input);
        self::assertIsScalar($input->value);
        self::assertIsString($input->value);
        self::assertEquals($expected, $input->value);
    }

    /**
     * @return array<array-key, array<array-key, InverseSortOption|string>>
     */
    public function sortOptionProvider(): array
    {
        return $this->autoAddDataProviderKeys([
            [InverseSortOption::name(), '-name'],
            [InverseSortOption::relativeDirectoryPath(), '-relativeDirectoryPath'],
        ]);
    }

    public function testItCanVerifyEnumValues(): void
    {
        self::assertIsArray(self::reflectionCallMethod(InverseSortOption::class, 'values'));
    }

    public function testSortOptionValues(): void
    {
        $values = InverseSortOption::toValues();
        self::assertIsArray($values);
        self::assertCount(2, $values);
        self::assertContains('-name', $values);
        self::assertContains('-relativeDirectoryPath', $values);
    }

    public function testSortOptionLabels(): void
    {
        $labels = InverseSortOption::toLabels();
        self::assertIsArray($labels);
        self::assertCount(2, $labels);
        self::assertContains('name', $labels);
        self::assertContains('relativeDirectoryPath', $labels);
    }
}
