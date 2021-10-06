# composer-manuscript

## Dependancies

Please first install [Composer](https://getcomposer.org/download/) from the official Composer website.

## Installing Manuscript

### Installing

Please note: this has been developed on a Linux system (Pop! OS 20.04 LTS) and has only been tested on that system. I'd really appreciate any feedback / help in making it work for other operating systems in the future, if anybody would like to help. :)

`composer global require davidpeach/composer-manuscript:^4.0`

### Uninstall

`composer global remove davidpeach/composer-manuscript`

## Usage

### Creating a new package

#### Create a composer package

```bash
# Change directory into the folder where all of your development packages will live
cd ~/your/package/development/folder
manuscript init

# You can also specify an install directory with the --install-dir flag
manuscript init --install-dir=/full/path/to/other/folder
```

Follow the terminal prompts to complete the command.

#### Create a Laravel package (using [Spatie’s Laravel Package Skeleton](https://github.com/spatie/package-skeleton-laravel)

This command will initialize a new repository in your github account, using the excellent Laravel package skeleton by Spatie.

It will then clone it to your local machine and begin Spatie’s configure script.

```bash
# Change directory into the folder where all of your development packages will live
cd ~/your/package/development/folder
manuscript init --type=spatie

# You can also specify an install directory with the --install-dir flag
manuscript init --type=spatie --install-dir=/full/path/to/other/folder
```

### Create a local Laravel installation “playground” for testing and playing.
Note: when running one of the `init` commands above, you can also pass the `--play` flag to automatically run the 
play command once the package is generated.

Whether you have just scaffolded a new package, or working with an existing one, you can have manuscript create a 
coding playground for you to manually work with it (only `Laravel` versions `6.x`, `7.x` and `8.x` are supported 
currently).

This will take away the manual steps of installing and symlinking the package into a framework for development.

```bash
# Change directory into your existing package directory
cd ~/your/package/development/existing-package

# Run the play command
manuscript play

# Running as part of the init command
manuscript init --type=spatie --play
```

## What is Manuscript?

Manuscript is a tool to quickly set up a local environment for developing a PHP composer package.

Manuscript can:
 - Setup a bare-bones composer package structure for you.
 - Setup a Laravel composer package using Spatie's excellent Laravel Package Skeleton.
 - Download your chosen PHP framework for you to manually test your package. (Laravel 6, 7 or 8 currently)
 - Install your composer package into the framework using `composer require`, with the `symlink` composer option in the 
   framework's composer file.

You will then be able to develop your package whilst it is installed in a local framework setting.
And because it is symlinked from your package development directory, you will see the changes reflected immediately.

## Why build Manuscript?

I wanted an easy-to-install, easy-to-use tool that would set up everything I needed to begin building a new composer package. And although the steps to get set up are relatively easy - depending on your own knowledge of course - I wanted to make it as simple as possible.

There are most likely other tools out there that will help you do this, but I just wanted to have a go at building something myself and sharing it back out there.

## Future additions
I do have some ideas for some small quality of life updates to this package. I will add them as [issues](https://github.com/davidpeach/composer-manuscript/issues) in the 
repository as I finalise my thoughts on them.

## Known Issues

### Local package version dependency bug

**Please Note**: There is currently a slightly edge-case issue when Manuscript tries to install multiple local packages you are working on. When one local package has another local package as a dependancy, sometimes the install will fail.

I'll explain the error I found as clearly as I can:

I have 2 packages im working on: **Base Package** and **Child Package**. **Child Package** has the **Base Package** listed as a dependancy at `v0.1.1-alpha`.

I firstly `git clone` both packages in order to work on them locally. Both are cloned down and checked out to the default `main` branch.

I then run `manuscript setup --current` inside each one, but the installation of one fails after the other has installed. This is because the checked out `main` branch of the **Base Package** does not match the require dependancy listed in the **Child Package** of `v0.1.1-alpha`.

**You can fix this** by opening the composer.json file that has the local package dependancy listed in it's require block, and altering the version constraint for that package _before_ installing it. 

For my example I temporarily alter `v0.1.1-alpha` in my **Child Package** to `*`. I then install the packages and revert the version back. Once the install is complete the files in the vendor directory are symlinked to the corresponding development folder. So there is no re-installing necessary.

This is a bug that I am working on ideas for a fix for currently. If you wish to submit a Pull Request with any fixes you know of, please do feel free. :)

### Thank You for looking.
