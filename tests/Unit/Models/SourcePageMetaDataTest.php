<?php

declare(strict_types=1);

namespace KickflipMonoTests\Unit\Models;

use Kickflip\Models\SourcePageMetaData;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\ReflectionHelpers;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class SourcePageMetaDataTest extends TestCase {
    use DataProviderHelpers;
    use ReflectionHelpers;

    public function testVerifyClassExists()
    {
        self::assertClassExists(SourcePageMetaData::class);
    }

    public function testItThrowsWhenCreatingInvalidMetaData()
    {
        $this->expectError();
        /**
         * @psalm-suppress InaccessibleMethod
         * @phpstan-ignore-next-line
         */
        new SourcePageMetaData('', '', '', '');
    }

    /**
     * @dataProvider sourceIteratorProvider
     */
    public function testItCanGetTypeFromSourcePageMetaData(SplFileInfo $splFileInfo)
    {
        $sourcePageMetaData = SourcePageMetaData::fromSplFileInfo($splFileInfo);
        self::assertHasProperties($sourcePageMetaData, ['viewName', 'implicitExtension']);
        self::assertIsString($sourcePageMetaData->getName());
        self::assertIsString($sourcePageMetaData->getFilename());
        self::assertIsString($sourcePageMetaData->getFullPath());
        self::assertIsString($sourcePageMetaData->getExtension());
        self::assertIsString($sourcePageMetaData->getMimeExtension());
        self::assertIsString($sourcePageMetaData->getType());
    }

    public function sourceIteratorProvider()
    {
        return $this->autoAddDataProviderKeys(iterator_to_array(
            Finder::create()
                ->files()
                ->in(dirname(__DIR__, 2) . '/sources')
                ->ignoreDotFiles(true)
                ->getIterator()
        ));
    }
}
