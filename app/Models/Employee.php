<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // ← ここを変更
use Illuminate\Notifications\Notifiable;

class Employee extends Authenticatable // ← Model から Authenticatable に変更
{
    use Notifiable;

    protected $table = 'employees';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'name', 'password'
    ];

    protected $hidden = [
        'password',
    ];

    public $timestamps = true;
}
