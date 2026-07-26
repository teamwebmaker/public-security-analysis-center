<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicShare extends Model
{
    protected $fillable = [
        'token',
        'created_by_user_id',
        'is_active',
        'revoked_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'revoked_at' => 'datetime',
    ];

    public function shareable()
    {
        return $this->morphTo();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function isUsable(): bool
    {
        return $this->is_active && $this->revoked_at === null;
    }
}
