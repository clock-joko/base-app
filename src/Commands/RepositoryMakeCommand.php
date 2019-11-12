<?php

namespace Clock\Baserepo\Commands;

use Illuminate\Console\GeneratorCommand;

use Illuminate\Support\Str;

class RepositoryMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create DataSources by Repository';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name . 'Repository');

        return $this->laravel['path'].'/'.str_replace('\\', '/', $name).'.php';
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
        return $rootNamespace . '\DataSources\\' . $dir . '\Repositories';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/repository.stub';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name) {
        $stub = $this->files->get($this->getStub());

        $stub = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);

        return $this->replaceUseNamespace($stub, $name);
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name . 'Repository');

        return str_replace('DummyClass', $class, $stub);
    }

    /**
     * @param $stub
     * @param $name
     * @return mixed
     */
    public function replaceUseNamespace($stub, $name)
    {
        $dir = Str::plural($this->getNameInput());
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        return str_replace(array('Dummys', 'Dummy', '$dummy'), array($dir, $class, '$' . lcfirst($class)), $stub);
    }
}
