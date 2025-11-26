<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskOccurrenceStatus extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'display_name',
    ];
    public function taskOccurrences()
    {
        return $this->hasMany(TaskOccurrence::class);
    }
}
