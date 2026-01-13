<?php

namespace Amadul\JsonObject\Concerns;

use Illuminate\Support\Facades\Validator;
use Amadul\JsonObject\Contracts\ValidatesJson;

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
