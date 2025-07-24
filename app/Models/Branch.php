<?php

namespace App\Models;

use App\Models\Traits\HasVisibilityScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    use HasVisibilityScope;

    protected $fillable = [
        'name',
        'address',
        'company_id',
        'visibility',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }


}
