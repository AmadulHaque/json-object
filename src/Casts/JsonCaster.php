<?php

namespace Amadul\JsonObject\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Amadul\JsonObject\JsonObject;

class JsonCaster implements CastsAttributes
{
    protected string $class;

    public function __construct(string $class)
    {
        $this->class = $class;
    }

    /**
     * Cast the given value.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function get($model, string $key, $value, array $attributes)
    {
        if (is_null($value)) {
            return new $this->class([]);
        }

        $decoded = json_decode($value, true);

        return new $this->class(is_array($decoded) ? $decoded : []);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return mixed
     */
    public function set($model, string $key, $value, array $attributes)
    {
        if (is_null($value)) {
            return null;
        }

        if ($value instanceof JsonObject) {
            return json_encode($value->toArray());
        }

        if ($value instanceof \Illuminate\Contracts\Support\Arrayable) {
            return json_encode($value->toArray());
        }

        if (is_array($value)) {
            return json_encode($value);
        }

        throw new InvalidArgumentException("The given value is not an instance of JsonObject or an array.");
    }
}
