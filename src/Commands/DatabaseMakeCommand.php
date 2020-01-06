<?php

namespace ClockIt\Baserepo\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class DatabaseMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create database and seeder';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Database';

    /**
     * @var string
     */
    private $flow = 'database';

    /**
     * Execute the console command.
     *
     * @return bool|null
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $status = $this->main();
        if ($status === false) {
            return false;
        }
        $this->flow = 'seeder';
        $status = $this->main();
        if ($status === false) {
            return false;
        }
        $this->info($this->type.' created successfully.');
    }

    /**
     * @return bool
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function main()
    {
        $name = $this->getNameInput();

        if ($this->flow === 'database') {
            $now = date('Y_m_d_His');
            $path = $this->getPath('migrations/' . $now . '_create_' . $name . '_table.php');
        } else {
            $name = $this->singularName($name);
            $path = $this->getPath('seeds/' . $name . 'Seeder.php');
        }

        // First we will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ((! $this->hasOption('force') ||
                ! $this->option('force')) &&
            $this->alreadyExists($this->getNameInput())) {
            $this->error($this->type.' already exists!');

            return false;
        }

        $this->files->put($path, $this->sortImports($this->buildClass($name)));
    }

    /**
     * @param $name
     * @return string
     */
    private function studlyName($name) : string
    {
        return Str::studly($name);
    }

    /**
     * @param $name
     * @return string
     */
    private function singularName($name) : string
    {
        return Str::singular(Str::studly($name));
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name) : string
    {
        return 'database/'. $name;
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace() : string
    {
        return 'database';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $name = $this->getNameInput();
        $dir = str_replace('Commands', 'Databases', __DIR__);
        if ($this->flow === 'database') {
            $dir .= '/migrations/xxxx_xx_xx_xxxxxx_create_'. $name . '_table.php';
        } else {
            $class = $this->singularName($name) . 'Seeder';
            $dir .= '/seeds/Origin' . $class . '.php';
        }

        return $dir;
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name) : string
    {
        if ($this->flow === 'database') {
            $class = 'Create' . $this->studlyName($name) . 'Table';
        } else {
            $class = $this->singularName($name) . 'Seeder';
        }

        return str_replace('DummyClass', $class, $stub);
    }

    /**
     * @param $stub
     * @return mixed
     */
    protected function replaceTableName($stub)
    {
        $name = $this->getNameInput();
        return str_replace('DummyTable', $name, $stub);
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());
        $stub = $this->replaceTableName($stub);

        return $this->replaceClass($stub, $name);
    }
}
