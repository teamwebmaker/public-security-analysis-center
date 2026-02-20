<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['source', 'full_name', 'phone', 'email', 'subject', 'message'];
    use HasFactory;

    protected $casts = [
        'read_at' => 'datetime',
    ];
}
