<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'source',
        'type',
        'full_name',
        'phone',
        'email',
        'subject',
        'message',
        'action_url',
        'action_label',
        'context',
    ];
    use HasFactory;

    protected $casts = [
        'read_at' => 'datetime',
        'context' => 'array',
    ];
}
