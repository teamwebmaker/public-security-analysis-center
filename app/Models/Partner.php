<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasVisibilityScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Partner extends Model
{
    use HasFactory;
    use HasVisibilityScope;

    protected $fillable = ['title', 'link', 'image', 'visibility'];

}
