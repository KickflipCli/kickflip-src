<?php

declare(strict_types=1);

namespace Kickflip\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\Factory;
use Kickflip\KickflipHelper;
use Kickflip\Logger;
use Kickflip\Models\PageData;
use Kickflip\Models\SiteData;
use Kickflip\SiteBuilder;
use Kickflip\SourcesLocator;
use League\CommonMark\Extension\FrontMatter\Output\RenderedContentWithFrontMatter;
use MallardDuck\LaravelTraits\Console\CommandManagesSections;

class BuildCommand extends BaseCommand
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
    protected $description = 'Build your site.';

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
        /**
         * @var string $env
         */
        $env = $this->input->getArgument('env');
        /**
         * @var bool $quiet
         */
        $quiet = filter_var($this->input->getOption('quiet'), FILTER_VALIDATE_BOOL);
        $prettyUrls = filter_var($this->input->getOption('pretty'), FILTER_VALIDATE_BOOL);

        # Load in the local projects config based on env...
        $this->includeEnvironmentConfig($env);
        $this->updateBuildPaths($env);

        $buildDest = KickflipHelper::buildPath();
        if (
            $quiet || !file_exists($buildDest) ||
            file_exists($buildDest) && $this->confirm('Overwrite "' . $buildDest . '"? ')
        ) {
            File::ensureDirectoryExists($buildDest);
            File::cleanDirectory($buildDest);
            $siteBuilder = new SiteBuilder($prettyUrls);
            $siteBuilder->build($this->output);
            $this->output->info("SUCCESS");
            $this->output->info("DONE");
            return static::SUCCESS;
        }
        $this->output->info("DONE");

        return static::FAILURE;
    }

    private function updateBuildPaths(string $env)
    {
        $basePaths = $this->app->get('kickflipCli')->get('paths.build');
        $basePaths['destination'] = Str::of($basePaths['destination'])->replaceEnv($env);
        // TODO: decide if we need a views entry in here too...
        $this->app->get('kickflipCli')->set('paths.build', $basePaths);
    }
}
