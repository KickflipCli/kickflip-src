<?php

namespace Kickflip\Commands;

use Illuminate\Support\Str;
use Illuminate\Contracts\View\Factory;
use Kickflip\Logger;
use Kickflip\Models\PageData;
use Kickflip\Models\SiteData;
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
                            {--pretty : Should the site use pretty URLs?}
                            {--c|cache : Should a cache be used when building the site?}
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
        /**
         * @var string $env
         */
        $env = $this->input->getArgument('env');
        /**
         * @var bool $quiet
         */
        $quiet = filter_var($this->input->getOption('quiet'), FILTER_VALIDATE_BOOL);
        $useCache = filter_var($this->input->getOption('cache'), FILTER_VALIDATE_BOOL);
        $prettyUrls = filter_var($this->input->getOption('pretty'), FILTER_VALIDATE_BOOL);

        # Load in the local projects config based on env...
        $this->includeEnvironmentConfig($env);
        $this->updateBuildPaths($env);
        $kickflipState = $this->app->get('kickflipCli');
        // TODO: fix later
        //if ($prettyUrls) {
        //    $this->app->instance('outputPathResolver', new PrettyOutputPathResolver());
        //}

        $buildDest = $kickflipState->get('paths.build.destination');


        $page = 'docs.getting-started';
        if (
            $quiet ||
            !file_exists($buildDest) ||
            file_exists($buildDest) && $this->confirm('Overwrite "' . $buildDest . '"? ')
        ) {
            $view = view($page)
                ->with('site', SiteData::fromConfig($kickflipState->get('site'), $kickflipState->get('siteNav', [])));
            $markdown = $view->render();
            $result = app(\Spatie\LaravelMarkdown\MarkdownRenderer::class)->convertToHtml($markdown);

            // Grab the front matter:
            if ($result instanceof RenderedContentWithFrontMatter) {
                $renderedMarkdown = $result->getContent();
                $frontMatter = $result->getFrontMatter();
                $pageData = PageData::fromFrontMatter($frontMatter);
                $this->overwriteSectionData($frontMatter['section'], (string) $renderedMarkdown);
                $view = view($frontMatter['extends'])
                    ->with('site', SiteData::fromConfig($kickflipState->get('site'), $kickflipState->get('siteNav')))
                    ->with('page', $pageData);
                dd(
                    $view->render()
                );
            }

            $this->output->info("SUCCESS");
        }
        $this->output->info("DONE");
    }

    private function updateBuildPaths(string $env)
    {
        $basePaths = $this->app->get('kickflipCli')->get('paths.build');
        $basePaths['destination'] = Str::replaceEnv($env, $basePaths['destination']);
        // TODO: decide if we need a views entry in here too...
        $this->app->get('kickflipCli')->set('paths.build', $basePaths);
    }
}
