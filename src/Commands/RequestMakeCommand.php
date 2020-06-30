<?php

namespace ClockIt\Baserepo\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Foundation\Console\RequestMakeCommand as DefaultRequestMakeCommand;
use Symfony\Component\Console\Input\InputOption;

class RequestMakeCommand extends DefaultRequestMakeCommand
{
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/request.stub';
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        $requests = str_replace('/', '\Requests\\', $this->getNameInput());
        $input = str_replace('/', '\\', $this->getNameInput());

        $name = str_replace($input, $requests, $name);

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
        return $rootNamespace . '\\' . config('base.directory.path');
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
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace(
            ['DummyNamespace', 'DummyRootNamespace', 'NamespacedDummyUserModel'],
            [$this->getNamespace($name) . '\Requests', $this->rootNamespace(), $this->userProviderModel()],
            $stub
        );

        return $this;
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
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

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

        return str_replace(['DummyDirectory', 'Dummys', 'Dummy', '$dummy'], [config('base.directory.path'), $dir, $class, '$' . lcfirst($class)], $stub);
    }
}
