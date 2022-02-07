<?php

declare(strict_types=1);

namespace KickflipMonoTests\DocsSite;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\View\Factory;
use Kickflip\KickflipHelper;
use Kickflip\KickflipKernel;
use Kickflip\Models\PageData;
use Kickflip\Models\SourcePageMetaData;
use KickflipMonoTests\DataProviderHelpers;
use KickflipMonoTests\Feature\BaseFeatureTestCase;

use function file_get_contents;
use function realpath;

abstract class DocsTestCase extends BaseFeatureTestCase
{
    use DataProviderHelpers;

    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        // Reset PageData to defaults
        PageData::$defaultExtendsView = 'layouts.master';
        PageData::$defaultExtendsSection = 'body';

        /**
         * @var \LaravelZero\Framework\Application $app
         */
        $app = require __DIR__ . '/../../packages/kickflip-cli/bootstrap/app.php';
        $basePath = realpath(__DIR__ . '/../../packages/kickflip-docs');
        KickflipHelper::basePath($basePath);
        KickflipHelper::setPaths($basePath);
        /**
         * @var KickflipKernel $kernel
         */
        $kernel = $app->make(Kernel::class);
        $kernel->bootstrap();
        $this->callAfterResolving($app, 'view', function ($view) {
            /**
             * @var Factory $view
             */
            $view->addLocation(realpath(__DIR__ . self::agnosticPath('/../views')));
        });

        return $app;
    }

    public function getDocsPageData(string $pageName): PageData
    {
        $allSources = KickflipHelper::getFiles(KickflipHelper::sourcePath())
            ->map(fn ($splFileInfo) => SourcePageMetaData::fromSplFileInfo($splFileInfo))
            ->filter(fn (SourcePageMetaData $sourcePageMetaData) => match ($sourcePageMetaData->getExtension()) {
                    'blade.php', 'md', 'markdown',
                    'md.blade.php', 'blade.md', 'blade.markdown' => true,
                    default => false,
            })->mapWithKeys(fn (SourcePageMetaData $value, $key) => [$value->getName() => $value])->toArray();
        // Create a SourcePageMetaData object
        $sourcePageMetaData = $allSources[$pageName];
        // Parse out the front matter page metadata
        $frontMatterData = KickflipHelper::getFrontMatterParser()
                ->parse(file_get_contents($sourcePageMetaData->getFullPath()))
                ->getFrontMatter() ?? [];

        // Create a PageData object
        return PageData::make($sourcePageMetaData, $frontMatterData);
    }
}
