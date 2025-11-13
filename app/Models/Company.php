<?php

namespace App\Models;

use App\Models\Traits\HasVisibilityScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    use HasVisibilityScope;

    protected $fillable = [
        'name',
        'economic_activity_type_id',
        'identification_code',
        'economic_activity_code',
        'high_risk_activities',
        'risk_level',
        'evacuation_plan',
        'visibility'
    ];


    public function users()
    {
        return $this->belongsToMany(User::class, 'company_leaders')->withTimestamps();
    }

    public function economic_activity_type()
    {
        return $this->belongsTo(EconomicActivityType::class);
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}
