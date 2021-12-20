<?php

declare(strict_types=1);

namespace Kickflip\Enums;

abstract class CliStateDirPaths
{
    public const Base = 'baseDir';
    public const Cache = 'cache';
    public const Resources = 'resources';
    public const Config = 'config';
    public const EnvConfig = 'env_config';
    public const BootstrapFile = 'bootstrapFile';
    public const NavigationFile = 'navigationFile';
    public const EnvNavigationFile = 'env_navigationFile';
    public const BuildBase = 'build';
    public const BuildSourcePart = 'source';
    public const BuildSource = self::BuildBase . '.' . self::BuildSourcePart;
    public const BuildDestinationPart = 'destination';
    public const BuildDestination = self::BuildBase . '.' . self::BuildDestinationPart;
}
