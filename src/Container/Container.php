<?php

namespace DavidPeach\Manuscript\Container;

use DavidPeach\Manuscript\Commands\ClearPlaygroundsCommand;
use DavidPeach\Manuscript\Commands\CreateCommand;
use DavidPeach\Manuscript\Commands\InitCommand;
use DavidPeach\Manuscript\Commands\ListPackagesCommand;
use DavidPeach\Manuscript\Commands\PlayCommand;
use DavidPeach\Manuscript\Commands\StatusCommand;
use DavidPeach\Manuscript\Finders\DevPackageFinder;
use DavidPeach\Manuscript\Finders\PlaygroundPackageFinder;
use DavidPeach\Manuscript\Frameworks\FrameworkChooser;
use DavidPeach\Manuscript\Models\Factories\DevPackageModelFactory;
use DavidPeach\Manuscript\Models\Factories\PlaygroundPackageModelFactory;
use DavidPeach\Manuscript\PackageBuilders\BasicPackageBuilder;
use DavidPeach\Manuscript\PackageBuilders\LaravelPackageBuilder;
use DavidPeach\Manuscript\PackageBuilders\PlaygroundPackageBuilder;
use DavidPeach\Manuscript\Utilities\ComposerFileManager;
use DavidPeach\Manuscript\Utilities\Config;
use DavidPeach\Manuscript\Utilities\GitCredentials;
use DavidPeach\Manuscript\Utilities\PackageInstaller;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Filesystem\Filesystem;

class Container extends ContainerBuilder
{
    public function __construct(ParameterBagInterface $parameterBag = null)
    {
        parent::__construct($parameterBag);

        // READ PACKAGE DEETS FROM A SEPARATE FILE ?
        $this->setParameter('app.name', 'Manuscript');
        $this->setParameter('app.version', '5.1.1');

        $this->registerServices();

        $this->registerCommands();

        $this->registerApp();
    }

    public function registerApp()
    {
        $this->register('app', Application::class)
            ->setArgument('name', '%app.name%')
            ->setArgument('version', '%app.version%')
            ->addMethodCall(method: 'add', arguments: [new Reference(id: 'clear_command')])
            ->addMethodCall(method: 'add', arguments: [new Reference(id: 'create_command')])
            ->addMethodCall(method: 'add', arguments: [new Reference(id: 'init_command')])
            ->addMethodCall(method: 'add', arguments: [new Reference(id: 'play_command')])
            ->addMethodCall(method: 'add', arguments: [new Reference(id: 'status_command')])
            ->addMethodCall(method: 'add', arguments: [new Reference(id: 'list_packages_command')]);
    }

    private function registerServices()
    {
        $this->register(id: 'filesystem_adapter', class: Filesystem::class);

        $this->register(id: 'config', class: Config::class)
            ->setArgument(key: 'filesystem', value: new Reference(id: 'filesystem_adapter'));

        $this->register(id: 'dev_packages_finder', class: DevPackageFinder::class)
            ->setArgument(key: 'modelFactory', value: new Reference(id: 'dev_package_model_factory'));

        $this->register(id: 'playground_packages_finder', class: PlaygroundPackageFinder::class)
            ->setArgument(key: 'modelFactory', value: new Reference(id: 'playground_package_model_factory'));

        $this->register(id: 'composer_file_manager', class: ComposerFileManager::class);

        $this->register(id: 'basic_package_builder', class: BasicPackageBuilder::class)
            ->setArgument(key: 'gitCredentials', value: new Reference(id: 'git_credentials'));

        $this->register(id: 'laravel_package_builder', class: LaravelPackageBuilder::class)
            ->setArgument(key: 'git', value: new Reference(id: 'git_credentials'));

        $this->register(id: 'git_credentials', class: GitCredentials::class);

        $this->register(id: 'package_installer', class: PackageInstaller::class)
            ->setArgument(key: 'composer', value: new Reference(id: 'composer_file_manager'));

        $this->register(id: 'framework_chooser', class: FrameworkChooser::class);

        $this->register(id: 'playground_package_builder', class: PlaygroundPackageBuilder::class);

        $this->register(id: 'dev_package_model_factory', class: DevPackageModelFactory::class)
            ->setArgument(key: 'composer', value: new Reference(id: 'composer_file_manager'));

        $this->register(id: 'playground_package_model_factory', class: PlaygroundPackageModelFactory::class)
            ->setArgument(key: 'composer', value: new Reference(id: 'composer_file_manager'));

    }

    private function registerCommands()
    {
        $this->register(id: 'init_command', class: InitCommand::class)
            ->setArgument(key: 'config', value: new Reference(id: 'config'))
            ->setArgument(key: 'playgroundFinder', value: new Reference(id: 'playground_packages_finder'))
            ->setArgument(key: 'devPackageFinder', value: new Reference(id: 'dev_packages_finder'));

        $this->register(id: 'clear_command', class: ClearPlaygroundsCommand::class)
            ->setArgument(key: 'playgrounds', value: new Reference(id: 'playground_packages_finder'));

        $this->register(id: 'create_command', class: CreateCommand::class)
            ->setArgument(key: 'basicPackageBuilder', value: new Reference('basic_package_builder'))
            ->setArgument(key: 'laravelPackageBuilder', value: new Reference('laravel_package_builder'));

        $this->register(id: 'play_command', class: PlayCommand::class)
            ->setArgument(key: 'devPackageModelFactory', value: new Reference(id: 'dev_package_model_factory'))
            ->setArgument(key: 'playgroundPackageModelFactory', value: new Reference(id: 'playground_package_model_factory'))
            ->setArgument(key: 'playgrounds', value: new Reference('playground_packages_finder'))
            ->setArgument(key: 'packageInstaller', value: new Reference('package_installer'))
            ->setArgument(key: 'frameworkChooser', value: new Reference('framework_chooser'))
            ->setArgument(key: 'playgroundPackageBuilder', value: new Reference('playground_package_builder'));

        $this->register(id: 'status_command', class: StatusCommand::class)
            ->setArgument(key: 'playgrounds', value: new Reference('playground_packages_finder'));

        $this->register(id: 'list_packages_command', class: ListPackagesCommand::class)
            ->setArgument(key: 'devPackages', value: new Reference(id: 'dev_packages_finder'));
    }
}