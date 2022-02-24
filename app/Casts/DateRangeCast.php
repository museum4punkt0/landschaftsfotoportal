<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class DateRangeCast implements CastsAttributes
{
    public function get($model, $key, $value, $attributes)
    {
        if (isset($attributes['value_daterange'])) {
            preg_match('/([\[\(]{1})(.*)\,(.*)([\]\)]{1})/', $attributes['value_daterange'], $matches);

            return new \App\DateRange($matches[2] ?? null, $matches[3] ?? null, $matches[1] ?? null, $matches[4] ?? null);
        }
        else {
            return new \App\DateRange(null, null);
        }
    }

    public function set($model, $key, $value, $attributes)
    {
        return [
            'value_daterange' => $this->serializeRange($value)
        ];
    }

    private function serializeRange($range)
    {
        return "[" .
            optional(optional($range)->from())->toDateString() .
            "," .
            optional(optional($range)->to())->toDateString() .
            "]";
    }
}
