<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    use HasFactory;

    protected $table = 'pengaturans';

    protected $fillable = [
        'key',
        'value',
        'label',
        'tipe',
    ];

    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, $value, $label = null, $tipe = 'text')
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'label' => $label ?? $key,
                'tipe' => $tipe,
            ]
        );
    }
}
