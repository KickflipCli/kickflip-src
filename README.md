<h1 align="center">Kickflip Site Builder</h1>
<p align="center">
    <strong>The monorepo for Kickflip site generator and the docs.</strong>
</p>

<p align="center">
    <a href="https://github.com/KickflipCli/kickflip-src"><img src="http://img.shields.io/badge/source-KickflipCli/kickflip--src-blue.svg?style=flat-square" alt="Source Code"></a>
    <a href="https://packagist.org/packages/Kickflip/kickflip-cli"><img src="https://img.shields.io/packagist/v/Kickflip/kickflip-cli.svg?style=flat-square&label=release" alt="Download Package"></a>
    <a href="https://php.net"><img src="https://img.shields.io/packagist/php-v/Kickflip/kickflip-cli.svg?style=flat-square&colorB=%238892BF" alt="PHP Programming Language"></a>
    <a href="https://github.com/KickflipCli/kickflip-src/blob/main/LICENSE.md"><img src="https://img.shields.io/packagist/l/Kickflip/kickflip-cli.svg?style=flat-square&colorB=darkcyan" alt="Read License"></a>
    <a href="https://github.com/KickflipCli/kickflip-src/actions/workflows/continuous-integration.yml"><img src="https://img.shields.io/github/workflow/status/KickflipCli/kickflip-src/Run%20CI%20Tests/main?style=flat-square&logo=github" alt="Build Status"></a>
    <a href="https://codecov.io/gh/KickflipCli/kickflip-src"><img src="https://img.shields.io/codecov/c/gh/KickflipCli/kickflip-src?label=codecov&logo=codecov&style=flat-square" alt="Codecov Code Coverage"></a>
    <a href="https://shepherd.dev/github/KickflipCli/kickflip-src"><img src="https://img.shields.io/endpoint?style=flat-square&url=https%3A%2F%2Fshepherd.dev%2Fgithub%2FKickflipCli%2Fkickflip-src%2Fcoverage" alt="Psalm Type Coverage"></a>
</p>
<!-- BADGES_END -->

## A message to Russian 🇷🇺 people

If you currently live in Russia, please read [this message](./ToRussianPeople.md).

<!-- DESC_START -->
## About


Kickflip is a Laravel Zero based CLI tool that generates a static HTML site from markdown and blade template files.
This is the monorepo that houses the [Kickflip-cli](https://github.com/KickflipCli/kickflip-cli) Site builder and the [docs](https://github.com/KickflipCli/kickflip-docs) for it.

### Repositories
- [kickflip-cli](https://github.com/KickflipCli/kickflip-cli) - The core Kickflip CLI project that provides the brains of Kickflip, kinda like laravel/framework
- [kickflip](https://github.com/KickflipCli/kickflip) - The starter project for Kickflip, equivilent to laravel/laravel
- [kickflip-docs](https://github.com/KickflipCli/kickflip-docs) - The official documentation for Kickflip
- [kickflip-router-nav-plugin](https://github.com/KickflipCli/kickflip-router-nav-plugin) **[DEPRECATED - Built-in as of 0.10]** - The official plugin for Router/Nav features

## Documentation

The official Kickflip Docs are at: https://kickflip.lucidinternets.com/

# Warning

Until this repo reaches V1.0.0, this is a work in progress and it ~~may~~ **will** _eat your cat_.  

Before V1 is released this repo may undergo a lot of changes with no backwards compatibility guarantees.

## The Roadmap to V1
- [x] At least 80% test coverage complete
- [x] Full implement Pretty URL build flag
- [ ] Complete the KickflipCLI docs repo
- [x] Review how URLs are generated by Helpers and such (should URLs be relative, or absolute? maybe make it a config option?)
- [x] Determine behavior for static files within sources (copy to build folder or consider better methods)
- [x] Explore idea about mocking traditional laravel routes based on source files (could allow for named routes usage 🤔)
- [x] Create a starter project
- [ ] Sort out workflows for CHANGELOG.md updates
- [x] Add README.md files to Kickflip-cli and Kickflip-docs
- [x] Hook into NodeJS to ensure required dependencies are installed