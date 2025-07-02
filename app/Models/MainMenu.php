<?php

namespace App\Models;

use App\Casts\JsonConvertCast;
use App\Models\Traits\HasVisibilityScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class MainMenu extends Model
{
    use HasFactory;
    use HasVisibilityScope;
    protected $fillable = ['title', 'sorted', 'visibility'];
    protected $casts = [
        'title' => JsonConvertCast::class,
    ];
    public function subMenu(): hasMany
    {
        return $this->hasMany(SubMenu::class);
    }
}
