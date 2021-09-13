<?php

namespace Kickflip\Commands;

use Kickflip\Logger;
use Illuminate\Config\Repository;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BaseCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        Logger::timing(__METHOD__, static::class);
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        Logger::timing(__METHOD__, static::class);
        return parent::run($input, $output);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        Logger::timing(__METHOD__, static::class);
        return parent::execute($input, $output);
    }


    protected function includeEnvironmentConfig(string $env)
    {
        /**
         * @var Repository $kickflipCliState
         */
        $kickflipCliState = app('kickflipCli');
        $envConfigPath = Str::replaceEnv($env, $kickflipCliState->get('paths.env_config'));
        if (file_exists($envConfigPath)) {
            $envSiteConfig = include $envConfigPath;
            $kickflipCliState->set('site', array_merge($kickflipCliState->get('site'), $envSiteConfig));
        }

        // TODO: actually test this...
        $envNavConfigPath = Str::replaceEnv($env, $kickflipCliState->get('paths.env_navigationFile'));
        if (file_exists($envNavConfigPath)) {
            $envNavConfig = include $envNavConfigPath;
            $kickflipCliState->set('siteNav', array_merge($kickflipCliState->get('siteNav'), $envNavConfig));
        }
    }
}
