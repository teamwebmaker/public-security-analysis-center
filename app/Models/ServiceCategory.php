<?php

namespace App\Models;

use App\Casts\JsonConvertCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    use HasFactory;
    protected $casts =[
        'name' => JsonConvertCast::class
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
