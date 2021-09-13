<?php

namespace Kickflip;

use Kickflip\Enums\ConsoleVerbosity;
use Kickflip\Enums\VerbosityFlag;
use LaravelZero\Framework\Kernel as BaseKernel;

class KickflipKernel extends BaseKernel
{
    /**
     * Kernel constructor.
     */
    public function __construct(
        \Illuminate\Contracts\Foundation\Application $app,
        \Illuminate\Contracts\Events\Dispatcher $events
    ) {
        \Illuminate\Support\Str::macro(
            'replaceEnv',
            static fn (string $env, string $subject) => static::replace('{env}', $env, $subject)
        );

        \Illuminate\Support\Collection::macro('findVerbosity', function () {
            $flags = $this->map(static fn ($value) => ltrim($value, '-'));
            $flags->shift();
            $flags = $flags->intersect(VerbosityFlag::toValues())
                ->map(static fn ($value) => VerbosityFlag::from($value));

            if ($flags->contains(VerbosityFlag::quiet())) {
                return ConsoleVerbosity::fromFlag(VerbosityFlag::quiet());
            }

            return $flags->map(static fn ($value) => ConsoleVerbosity::fromFlag($value))
                    ->sortByDesc(static fn ($value) => $value->value)->first() ?? ConsoleVerbosity::normal();
        });

        Logger::timing(__METHOD__);
        parent::__construct($app, $events);
    }

    /**
     * {@inheritdoc}
     */
    public function handle($input, $output = null)
    {
        Logger::timing(__METHOD__);
        # Globally sets the verbosity so that the app itself, not just commands know the verbosity level
        $this->app->get('kickflipCli')
            ->set('output.verbosity', collect(explode(' ', (string) $input))->findVerbosity());
        return parent::handle($input, $output);
    }
}
