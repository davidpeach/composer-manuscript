# composer-manuscript

_The playgrounds functionality is currently broken but will be fixing this very soon_

Manuscript is a tool to help you with developing and testing composer packages on your computer.

## Installation

`composer global require davidpeach/composer-manuscript`

## How it works

Once installed, you just assign a local directory as being a "manuscript" directory.

So you could do this:

```bash
cd ~/my-local-packages

manuscript init
```

The `manuscript init` command just creates a `.manuscript` file in the current directory, along with two folders:

- packages
- playgrounds

**./packages** is where you place any packages that you are building.

**./playgrounds** is where any known frameworks can be installed for you and have your package in development installed into.
So for example, you could be developing a laravel package and want a local laravel installation to manually test things in.
With the `manuscript play` command, you can tell Manuscript to download a copy of Laravel and use the repository symlink functionality to install your package into that Laravel installation for testing.

## How to contribute

### Setting up

    1. Clone this repo.
    2. Run `composer install`.
    3. Run `composer test` to check the test suite passes on your machine.

### Local Scratchpad


### Thank You for looking.
