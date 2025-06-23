<?php

namespace App\Models;

use App\Casts\JsonConvertCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    protected $fillable = ['name', 'visibility'];
    use HasFactory;
    protected $casts = [
        'name' => JsonConvertCast::class
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
