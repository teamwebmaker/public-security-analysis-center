<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class MainMenu extends Model
{
    use HasFactory;
    public function subMenu(): hasMany
    {
        return $this -> hasMany(SubMenu::class);
    }
}
