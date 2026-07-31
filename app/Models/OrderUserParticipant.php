<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderUserParticipant extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'signed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
