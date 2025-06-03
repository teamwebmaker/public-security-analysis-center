<?php

namespace App\Models;

use App\Casts\JsonConvertCast;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $casts = [
        'title' => JsonConvertCast::class,
        'description' => JsonConvertCast::class
    ];
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }
}
