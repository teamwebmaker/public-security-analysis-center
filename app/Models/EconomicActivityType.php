<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EconomicActivityType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
    ];


    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
