<?php

namespace Laravel\JsonObject\Concerns;

use Illuminate\Support\Facades\Log;

trait HasLogging
{
    protected function log(string $message, array $context = []): void
    {
        if (! config('json-object.features.logging', false)) {
            return;
        }

        $channel = config('json-object.log_channel');
        
        Log::channel($channel)->info("[JsonObject] {$message}", array_merge([
            'class' => static::class,
        ], $context));
    }
}
