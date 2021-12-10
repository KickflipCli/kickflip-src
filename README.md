<h1 align="center">Kickflip Site Builder</h1>
<p align="center">
    <strong>The monorepo for Kickflip site generator and the docs.</strong>
</p>

## About

Kickflip is a Laravel Zero based CLI tool that generates a static HTML site from markdown and blade template files.
This is the monorepo that houses the [Kickflip-cli](https://github.com/mallardduck/kickflip-cli) Site builder and the [docs](https://github.com/mallardduck/kickflip-docs) for it.


# Warning

Until this repo reaches V1.0.0, this is a work in progress and it ~~may~~ **will** _eat your cat_.  

Before V1 is released this repo may undergo a lot of changes with no backwards compatibility guarantees.

## The Roadmap to V1
- [ ] Create a starter project
- [ ] Complete the KickflipCLI docs repo
- [ ] Sort out workflows for CHANGELOG.md updates
- [ ] Add README.md files to Kickflip-cli and Kickflip-docs
- [ ] Review the way URLs are generated (consider adding a config option for this)
- [x] Hook into NodeJS to ensure required dependencies are installed - complete with [only use shiki fetcher inside build command](https://github.com/mallardduck/kickflip-monorepo/commit/ce365d5201858b29933e5ef8465439f0d90bb016)
- [ ] Ensure static files within sources are copied to the build directory (or consider better methods)