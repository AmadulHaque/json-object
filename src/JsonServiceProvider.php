<?php

namespace Amadul\JsonObject;

use Illuminate\Support\ServiceProvider;
use Amadul\JsonObject\Commands\MakeJsonCommand;

class JsonServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/json-object.php' => config_path('json-object.php'),
            ], 'json-object-config');

            $this->commands([
                MakeJsonCommand::class,
            ]);
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/json-object.php', 'json-object'
        );
    }
}
