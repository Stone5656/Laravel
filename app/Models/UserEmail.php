<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserEmail extends BaseModel
{
    use HasFactory;

    protected $fillable = ['id', 'user_id', 'email', 'is_primary', 'verified_at'];

    protected $casts = [
        'is_primary' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
