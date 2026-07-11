<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = ['key', 'value', 'autoload'];

    protected function casts(): array
    {
        return [
            'autoload' => 'boolean',
        ];
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $option = static::where('key', $key)->first();
        return $option ? $option->value : $default;
    }

    public static function setValue(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
