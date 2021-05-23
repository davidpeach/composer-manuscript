# composer-manuscript

Please note that this tool is in the very early stages of development. It works well for my needs but does need a lot of polish going forwards. There will also be things I will have not thought of or things I've overlooked. Pull requests are very welcome. :)

## Install (still in Alpha)

`composer global require davidpeach/composer-manuscript:dev-main`

## Uninstall

`composer global remove davidpeach/composer-manuscript`

## Usage

```
# Change directory into the folder where all of your development packages will live
cd ~/your/package/development/folder
manuscript setup
```
You will be asked to enter the full `namespace/package-name` that you want your package to have.
You will also be asked which framework you want to develop your package with (only `Laravel` versions `6.x`, `7.x` and `8.x` are supported currently)

## What is Manuscript?

Manuscript is a tool to quickly set up a local environment for developing a PHP composer package.

Manuscript will:
 - Setup a bare-bones composer package structure.
 - Download your chosen PHP framework alongside the bare-bones package
 - Install your bare-bones package into the framework using `composer require`, with the `symlink` composer option in the framework's composer file.

You will then be able to develop your package whilst it is installed in a framework setting. And because it is symlinked from your package development directory, you will see the changes reflected immediately.

## Why build Manuscript?

I wanted an easy-to-install, easy-to-use tool that would set up everything I needed to begin building a new composer package. And although the steps to get set up are relatively easy - depending on your own knowledge of course - I wanted to make it as simple as possible.

There are most likely other tools out there that will help you do this, but I just wanted to have a go at building something myself and sharing it back out there.
