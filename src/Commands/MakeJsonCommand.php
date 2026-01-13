<?php

namespace Laravel\JsonObject\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:json')]
class MakeJsonCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new JSON object class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Json Object';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__.'/../../stubs/json.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return config('json-object.namespace', $rootNamespace . '\\Json');
    }
}
