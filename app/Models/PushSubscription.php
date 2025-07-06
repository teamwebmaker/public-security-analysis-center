<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PushSubscription extends Model
{
    use HasFactory;
    protected $fillable = [
        'subscriptions',
        'approved',
        'browser',
        'os',
        'ip_address',
        'country',
        'city',
        'user_agent'
    ];

    protected $casts = [
        'subscriptions' => 'array',
        'approved' => 'boolean'

    ];

    // Scope for approved subscriptions
    public function scopeApproved($query)
    {
        return $query->where('approved', true);
    }
}