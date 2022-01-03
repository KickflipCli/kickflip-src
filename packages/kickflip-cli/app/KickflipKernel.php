<?php

declare(strict_types=1);

namespace Kickflip;

use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Kickflip\Enums\ConsoleVerbosity;
use Kickflip\Enums\VerbosityFlag;
use LaravelZero\Framework\Kernel as BaseKernel;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class KickflipKernel extends BaseKernel
{
    /**
     * Kernel constructor.
     */
    public function __construct(
        \Illuminate\Contracts\Foundation\Application $app,
        \Illuminate\Contracts\Events\Dispatcher $events
    ) {
        Stringable::macro(
            'replaceEnv',
            function(string $env) {
                /**
                 * @var Stringable $this
                 */
                return $this->replace('{env}', $env);
        });

        Stringable::macro('findVerbosity', function () {
            /**
             * @var Stringable $this
             */
            $flags = $this->explode(' ')->map(static fn ($value) => ltrim($value, '-'));
            $flags->shift();
            $flags = $flags->intersect(['q', ...array_values(VerbosityFlag::toValues())])
                ->map(static fn ($value) => ('q' === $value) ? 'quiet' : $value)
                ->map(static fn ($value) => VerbosityFlag::from($value));

            // Ensure we catch both long/short flags for quite mode.
            if ($flags->contains(VerbosityFlag::quiet()) || $flags->contains('q')) {
                return ConsoleVerbosity::fromFlag(VerbosityFlag::quiet());
            }

            return $flags->map(static fn ($value) => ConsoleVerbosity::fromFlag($value))
                    ->sortByDesc(static fn ($value) => $value->value)->first() ?? ConsoleVerbosity::normal();
        });

        Logger::timing(__METHOD__);
        parent::__construct($app, $events);
    }

    /**
     * @param  InputInterface  $input
     * @param  OutputInterface|null  $output
     * @return int
     */
    public function handle($input, $output = null)
    {
        /**
         * @var ArgvInput $input
         */
        $castInput = (string) $input;
        Logger::timing(__METHOD__);
        # Globally sets the verbosity so that the app itself, not just commands know the verbosity level
        $this->app->get('kickflipCli')
            ->set('output.verbosity', Str::of($castInput)->findVerbosity());
        return parent::handle($input, $output);
    }
}
