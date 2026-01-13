<?php

namespace Laravel\JsonObject;

use Illuminate\Contracts\Database\Eloquent\Castable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Traits\Macroable;
use JsonSerializable;
use Laravel\JsonObject\Casts\JsonCaster;

abstract class JsonObject implements Castable, Arrayable, Jsonable, JsonSerializable
{
    use Macroable;

    protected array $attributes = [];
    protected array $schema = [];
    protected array $casts = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $this->filterAttributes($attributes);
        $this->castAttributes();
        $this->initializeTraits();
    }

    public static function from(array $value): static
    {
        return new static($value);
    }

    protected function initializeTraits(): void
    {
        foreach (class_uses_recursive($this) as $trait) {
            if (method_exists($this, $method = 'initialize'.class_basename($trait))) {
                $this->{$method}();
            }
        }
    }

    /**
     * Get the caster class to use when casting from / to this cast target.
     *
     * @param  array  $arguments
     * @return object|string
     */
    public static function castUsing(array $arguments): object|string
    {
        return new JsonCaster(static::class);
    }

    public function toArray(): array
    {
        // Recursively convert nested JsonObjects to array
        return array_map(function ($value) {
            if ($value instanceof Arrayable) {
                return $value->toArray();
            }
            return $value;
        }, $this->attributes);
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    protected function filterAttributes(array $attributes): array
    {
        if (empty($this->schema)) {
            return $attributes;
        }

        // Simple filtering based on schema keys
        // Schema can be ['key', 'nested.key']
        // For simplicity, we'll just check top level or implement dot notation filtering if needed.
        // Doc example shows 'dimensions.width', so dot notation is expected.
        // However, standard array_intersect_key only works on top level.
        // For now, let's just accept all if schema is complex, or maybe just return all.
        // The doc says "Schema whitelist".
        
        // Let's implement a basic whitelist approach.
        // Since filtering nested arrays is complex, we might skip strict filtering for v1 
        // unless we want to use `Arr::only`.
        // But `Arr::only` works with dot notation.
        
        return \Illuminate\Support\Arr::only($attributes, $this->schema);
    }

    protected function castAttributes(): void
    {
        foreach ($this->casts as $key => $type) {
            $value = data_get($this->attributes, $key);
            if ($value !== null) {
                $castedValue = $this->castValue($type, $value);
                data_set($this->attributes, $key, $castedValue);
            }
        }
    }

    protected function castValue(string $type, mixed $value): mixed
    {
        if (class_exists($type) && is_subclass_of($type, JsonObject::class)) {
            return $type::from((array) $value);
        }

        return match ($type) {
            'int', 'integer' => (int) $value,
            'float', 'real', 'double' => (float) $value,
            'string' => (string) $value,
            'bool', 'boolean' => (bool) $value,
            'array' => (array) $value,
            'datetime', 'date' => $this->asDateTime($value),
            // Add more types or custom casters here
            default => $value,
        };
    }

    /**
     * Cast value to DateTime.
     */
    protected function asDateTime(mixed $value): ?\Illuminate\Support\Carbon
    {
        if ($value instanceof \DateTimeInterface) {
            return \Illuminate\Support\Carbon::instance($value);
        }

        return \Illuminate\Support\Carbon::parse($value);
    }
}
