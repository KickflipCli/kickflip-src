<?php

declare(strict_types=1);

namespace KickflipMonoTests\Feature\Commands;

use Illuminate\Testing\PendingCommand;
use KickflipMonoTests\TestCase;

use function collect;
use function explode;

class BuildCommandHelpTest extends TestCase
{
    public function testBuildCommandHelpFlag()
    {
        /**
         * @var PendingCommand $pendingCommand
         */
        $pendingCommand = $this->artisan('build', ['--help'])
            ->assertExitCode(0);

        // phpcs:disable
        $expectedOutput = <<<'HEREDOC'
Description:
  Build your website project.

Usage:
  build [options] [--] [<env>]

Arguments:
  env                    What environment should we use to build? [default: "local"]

Options:
      --pretty[=PRETTY]  Should the site use pretty URLs? [default: "true"]
  -h, --help             Display help for the given command. When no command is given display help for the list command
  -q, --quiet            Do not output any message
  -V, --version          Display this application version
      --ansi|--no-ansi   Force (or disable --no-ansi) ANSI output
  -n, --no-interaction   Do not ask any interactive question
      --env[=ENV]        The environment the command should run under
  -v|vv|vvv, --verbose   Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
HEREDOC;
        // phpcs:enable

        collect(explode("\n", $expectedOutput))
            ->filter(fn ($value) => '' !== $value)
            ->map(fn ($value) => $pendingCommand->expectsOutput($value));
    }
}
