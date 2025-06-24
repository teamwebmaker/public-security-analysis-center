<?php

namespace App\Models;

use App\Casts\JsonConvertCast;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasVisibilityScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceCategory extends Model
{
    use HasFactory;
    use HasVisibilityScope;

    protected $fillable = ['name', 'visibility'];
    protected $casts = [
        'name' => JsonConvertCast::class
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
