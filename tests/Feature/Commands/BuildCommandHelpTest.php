<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

beforeEach(function () {
    $buildPath = (string) Str::of(\Kickflip\KickflipHelper::namedPath(\Kickflip\Enums\CliStateDirPaths::BuildDestination))->replaceEnv('local');
    if (is_dir($buildPath)) {
        File::deleteDirectory($buildPath);
    }
});

test('build command help flag', function () {
    /**
     * @var \Illuminate\Testing\PendingCommand $pendingCommand
     */
    $pendingCommand = $this->artisan('build', ['--help'])
        ->assertExitCode(0);

$expectedLines = collect(explode("\n", <<<HEREDOC
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
HEREDOC))
    ->filter(fn($value) => '' !== $value)
    ->map(fn($value) => $pendingCommand->expectsOutput($value));
});
