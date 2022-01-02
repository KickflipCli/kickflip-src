<?php

declare(strict_types=1);

namespace KickflipMonoTests\Unit\Enums;

use Kickflip\Enums\CliStateDirPaths;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\ReflectionHelpers;
use PHPUnit\Framework\TestCase;

class CliStateDirPathsEnumTest extends TestCase
{
    use DataProviderHelpers;
    use ReflectionHelpers;

    public function testCliStateDirPathsEnumExists()
    {
        self::assertClassExists(CliStateDirPaths::class);
    }

    /**
     * @dataProvider cliStateDirPathsConstantsProvider
     */
    public function testItCanVerifyCliStateDirPathsConstants(string $constName)
    {
        self::assertTrue(defined('Kickflip\Enums\CliStateDirPaths::' . $constName));
    }

    public function cliStateDirPathsConstantsProvider(): array
    {
        return $this->autoAddDataProviderKeys([
            ['Base'],
            ['Cache'],
            ['Resources'],
            ['Config'],
            ['ConfigFile'],
            ['EnvConfig'],
            ['BootstrapFile'],
            ['BuildBase'],
            ['BuildSourcePart'],
            ['BuildSource'],
            ['BuildDestinationPart'],
            ['BuildDestination'],
        ]);
    }

    /**
     * @dataProvider cliStateDirPathsValuesProvider
     */
    public function testItCanVerifyCliStateDirPathsValues(string $constName, string $expected)
    {
        self::assertEquals($expected, constant('Kickflip\Enums\CliStateDirPaths::' . $constName));
    }

    public function cliStateDirPathsValuesProvider(): array
    {
        return $this->autoAddDataProviderKeys([
            [
                'Base',
                'baseDir',
            ],
            [
                'Cache',
                'cache',
            ],
            [
                'Resources',
                'resources',
            ],
            [
                'Config',
                'config_dir',
            ],
            [
                'ConfigFile',
                'config_file',
            ],
            [
                'EnvConfig',
                'env_config',
            ],
            [
                'BootstrapFile',
                'bootstrapFile',
            ],
            [
                'BuildBase',
                'build',
            ],
            [
                'BuildSourcePart',
                'source',
            ],
            [
                'BuildSource',
                'build.source',
            ],
            [
                'BuildDestinationPart',
                'destination',
            ],
            [
                'BuildDestination',
                'build.destination',
            ],
        ]);
    }
}
