<?php

namespace Kickflip\Enums;

enum CliStateDirPaths
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
    public const BuildSource = 'source';
    public const BuildDestination = 'destination';
}
