<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'link', 'image', 'visibility'];

    // Automatically apply "visibility = 1" to all queries
    protected static function booted(): void
    {
        static::addGlobalScope('visible', function (Builder $builder) {
            if (!request()->is('admin/*')) {
                $builder->where('visibility', '1');
            }
        });
    }

}
