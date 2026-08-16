<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialEquipmentSignature extends Model
{
    protected $fillable = [
        'material_equipment_id',
        'user_id',
        'signed_at',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function materialEquipment()
    {
        return $this->belongsTo(MaterialEquipment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
