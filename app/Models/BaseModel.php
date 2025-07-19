<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

abstract class BaseModel extends Model
{
    protected static function boot()
    {
        parent::boot();

        foreach (class_uses_recursive(static::class) as $trait) {
            $method = 'boot' . class_basename($trait);
            if (method_exists(static::class, $method)) {
                forward_static_call([static::class, $method]);
            }
        }

        static::creating(function ($model) {
            if (in_array('id', $model->getFillable()) && empty($model->id)) {
                $uuid = (string) Str::uuid();
                if (!Str::isUuid($uuid)) {
                    throw ValidationException::withMessages(['id' => 'Invalid UUID']);
                }
                $model->id = $uuid;
            }
        });
    }
}
