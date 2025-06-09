<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class JsonConvertCast implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        // Return as object for ->ka syntax
        return json_decode($value); // no `true` => returns stdClass
    }

    public function set($model, string $key, $value, array $attributes)
    {
        // Store as JSON with visible Unicode characters
        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }
}
