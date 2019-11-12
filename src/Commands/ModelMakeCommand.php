<?php

namespace Clock\Baserepo\Commands;

use Illuminate\Support\Str;
use Illuminate\Foundation\Console\ModelMakeCommand as DefaultModelMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class ModelMakeCommand extends DefaultModelMakeCommand
{
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (parent::handle() === false && ! $this->option('force')) {
            return false;
        }

        if ($this->option('all')) {
            $this->input->setOption('factory', true);
            $this->input->setOption('migration', true);
            $this->input->setOption('controller', true);
            $this->input->setOption('resource', true);
            $this->input->setOption('repository', true);
            $this->input->setOption('interface', true);
        }

        if ($this->option('factory')) {
            $this->createFactory();
        }

        if ($this->option('migration')) {
            $this->createMigration();
        }

        if ($this->option('controller') || $this->option('resource')) {
            $this->createController();
        }

        if ($this->option('repository')) {
            $this->createRepository();
        }

        if ($this->option('interface')) {
            $this->createInterface();
        }
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace) : string
    {
        $dir = Str::plural($this->getNameInput());
        return $rootNamespace . '\DataSources\\' . $dir;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['all', 'a', InputOption::VALUE_NONE, 'Generate a migration, factory, and resource controller for the model'],

            ['controller', 'c', InputOption::VALUE_NONE, 'Create a new controller for the model'],

            ['factory', 'f', InputOption::VALUE_NONE, 'Create a new factory for the model'],

            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the model already exists'],

            ['migration', 'm', InputOption::VALUE_NONE, 'Create a new migration file for the model'],

            ['pivot', 'p', InputOption::VALUE_NONE, 'Indicates if the generated model should be a custom intermediate table model'],

            ['resource', 'r', InputOption::VALUE_NONE, 'Indicates if the generated controller should be a resource controller'],

            ['repository', 'y', InputOption::VALUE_NONE, 'Create a new repository for the model'],

            ['interface', 'i', InputOption::VALUE_NONE, 'Create a new interface for the model'],
        ];
    }

    /**
     * Create a repository for the model.
     *
     * @return void
     */
    protected function createRepository()
    {
        $modelName = Str::studly(class_basename($this->argument('name')));

        $this->call('make:repository', [
            'name' => $modelName,
        ]);
    }

    /**
     * Create a interface for the model.
     *
     * @return void
     */
    protected function createInterface()
    {
        $modelName = Str::studly(class_basename($this->argument('name')));

        $this->call('make:interface', [
            'name' => $modelName,
        ]);
    }
}
