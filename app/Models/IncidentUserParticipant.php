<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidentUserParticipant extends Model
{
    protected $fillable = [
        'incident_id',
        'user_id',
        'signed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
