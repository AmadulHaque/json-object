<?php

namespace Laravel\JsonObject\Concerns;

use Illuminate\Support\Facades\Validator;
use Laravel\JsonObject\Contracts\ValidatesJson;

trait HasValidation
{
    public function validate(): void
    {
        if (! $this instanceof ValidatesJson) {
            return;
        }

        $validator = Validator::make($this->toArray(), $this->rules());

        $validator->validate();
    }
}
