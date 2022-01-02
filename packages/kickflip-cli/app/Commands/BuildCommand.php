<?php

declare(strict_types=1);

namespace Kickflip\Commands;

use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Facades\File;
use Kickflip\Events\BeforeConfigurationLoads;
use Kickflip\KickflipHelper;
use Kickflip\Logger;
use Kickflip\SiteBuilder\SiteBuilder;
use LaravelZero\Framework\Commands\Command;
use MallardDuck\LaravelTraits\Console\CommandManagesSections;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use function file_exists;
use function filter_var;

use const FILTER_VALIDATE_BOOL;

class BuildCommand extends Command
{
    use CommandManagesSections;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'build
                            {--pretty=true : Should the site use pretty URLs?}
                            {env=local : What environment should we use to build?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Build your website project.';

    protected Factory $viewFactory;

    /**
     * {@inheritdoc}
     */
    public function __construct(Factory $viewFactory)
    {
        $this->viewFactory = $viewFactory;
        Logger::timing(__METHOD__, static::class);
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Logger::setOutput($this->output);
        [$env, $quiet, $prettyUrls] = $this->initCommandVars();

        BeforeConfigurationLoads::dispatch();
        // Load in the local projects config based on env...
        SiteBuilder::includeEnvironmentConfig($env);
        SiteBuilder::updateBuildPaths($env);

        $buildDest = KickflipHelper::buildPath();
        if (
            $quiet || !file_exists($buildDest) ||
            (file_exists($buildDest) && $this->confirm('Overwrite "' . $buildDest . '"? '))
        ) {
            File::ensureDirectoryExists($buildDest);
            File::cleanDirectory($buildDest);
            $this->output->writeln('<info>Starting site build...</info>');
            $siteBuilder = new SiteBuilder($prettyUrls);
            $siteBuilder->build($this->output);
            $this->output->success('Completed building site.');

            return static::SUCCESS;
        }
        $this->output->error('Done, did not build.');

        return static::FAILURE;
    }

    /**
     * @return array{env: string, quiet:bool, prettyUrls: bool,}
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function initCommandVars(): array
    {
        /**
         * @var string $env
         */
        $env = $this->input->getArgument('env');
        /**
         * @var bool $quiet
         */
        $quiet = filter_var($this->input->getOption('quiet'), FILTER_VALIDATE_BOOL);
        // Set global state of pretty URL status
        $prettyUrls = filter_var($this->input->getOption('pretty'), FILTER_VALIDATE_BOOL);
        $this->app->get('kickflipCli')->set('prettyUrls', $prettyUrls);

        return [$env, $quiet, $prettyUrls];
    }
}
