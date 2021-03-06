<?php

declare(strict_types=1);

namespace Kickflip\Enums;

/**
 * @internal
 */
abstract class CliStateDirPaths
{
    public const Base = 'baseDir';
    public const Cache = 'cache';
    public const Resources = 'resources';
    public const Config = 'config_dir';
    public const ConfigFile = 'config_file';
    public const EnvConfig = 'env_config';
    public const BootstrapFile = 'bootstrapFile';
    public const BuildBase = 'build';
    public const BuildSourcePart = 'source';
    public const BuildSource = self::BuildBase . '.' . self::BuildSourcePart;
    public const BuildDestinationPart = 'destination';
    public const BuildDestination = self::BuildBase . '.' . self::BuildDestinationPart;
    public const EnvBuildDestinationPart = 'env_' . self::BuildDestinationPart;
    public const EnvBuildDestination = self::BuildBase . '.' . self::EnvBuildDestinationPart;
}
