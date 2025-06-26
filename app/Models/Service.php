<?php

namespace App\Models;

use App\Casts\JsonConvertCast;
use App\Models\Traits\HasVisibilityScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    use HasVisibilityScope;

    protected $fillable = [
        "title",
        "description",
        "image",
        "visibility",
        "sortable",
        "service_category_id",
    ];
    protected $casts = [
        "title" => JsonConvertCast::class,
        "description" => JsonConvertCast::class,
    ];
    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, "service_category_id");
    }
}
