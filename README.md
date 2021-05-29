# composer-manuscript

## Dependancies

Please install [Composer](https://getcomposer.org/download/) from the official website. Manuscript scaffolds new packages using `composer init`.

Please install `git` for your system also. Manuscript uses git when trying to determine the correct author value when scaffolding the fresh package.

## Installing Manuscript

Please note: this has been developed on a Linux system (Pop! OS 20.04 LTS) and has only been tested on that system. I'd really appreciate any feedback / help in making it work for other operating systems in the future, if anybody would like to help. :)

`composer global require davidpeach/composer-manuscript:1.0`

## Uninstall

`composer global remove davidpeach/composer-manuscript`

## Usage

### Scaffolding a fresh package

```bash
# Change directory into the folder where all of your development packages will live
cd ~/your/package/development/folder
manuscript setup

# You can also specify an install directory with the --install-dir flag
manuscript setup --install-dir=/path/to/other/folder
```

You will be asked to enter the full `namespace/package-name` that you want your package to have, as well as some other settings that will be used to generate your new package's composer file.

### Setting up with an existing package
```bash
# Change directory into your existing package directory
cd ~/your/package/development/folder/existing-package

# Run the setup command with the --current flag
manuscript setup --current
```

### Playground setup
Whether you are scaffolding a new package, or working with an existing one, you will also be asked which framework you want to develop your package with (only `Laravel` versions `6.x`, `7.x` and `8.x` are supported currently).

Manuscript will also install the package into the playground for you, using composer's symlink path option. Which means you can work on your package and see the changes update instantly.

### Example code included
I have included a small random quote generator class with fresh packages scaffolded. A route will also be added to the playground that will just return the value of that class's static method. This is just to show you something working from the start.

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

## Future additions
I do have some ideas for some small quality of life updates to this package. I will add them as issues in the repository as I finalise my thoughts on them.

Thank You for looking.
