<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidentExternalParticipant extends Model
{
    protected $fillable = [
        'incident_id',
        'full_name',
        'phone',
        'signed_at',
        'signed_marked_by_user_id',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function signedMarkedBy()
    {
        return $this->belongsTo(User::class, 'signed_marked_by_user_id');
    }
}
