<?php
namespace Clock\Baserepo\Providers;

use Clock\Baserepo\Commands\InterfaceMakeCommand;
use Clock\Baserepo\Commands\RepositoryMakeCommand;
use Clock\Baserepo\Commands\ModelMakeCommand;
use Clock\Baserepo\Commands\DatabaseMakeCommand;
use Illuminate\Foundation\Providers\ArtisanServiceProvider;

class CommandServiceProvider extends ArtisanServiceProvider
{
    protected $customCommands = [
        'RepositoryMake' => 'command.repository.make',
        'InterfaceMake' => 'command.interface.make',
        'DatabaseMake' => 'command.database.make',
    ];
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands(array_merge(
            $this->commands, $this->devCommands, $this->customCommands
        ));
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerModelMakeCommand()
    {
        $this->app->singleton('command.model.make', function ($app) {
            return new ModelMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerRepositoryMakeCommand()
    {
        $this->app->singleton('command.repository.make', function ($app) {
            return new RepositoryMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerInterfaceMakeCommand()
    {
        $this->app->singleton('command.interface.make', function ($app) {
            return new InterfaceMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerDatabaseMakeCommand()
    {
        $this->app->singleton('command.database.make', function ($app) {
            return new DatabaseMakeCommand($app['files']);
        });
    }
}
