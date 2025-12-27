<?php

namespace App\Models;

use App\Models\Traits\HasVisibilityScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    use HasVisibilityScope;


    protected $fillable = [
        'branch_id',
        'branch_name_snapshot',
        'service_id',
        'service_name_snapshot',
        'recurrence_interval',
        'is_recurring',
        'archived',
        'visibility',
    ];

    protected $casts = [
        'is_recurring' => 'bool',
        'recurrence_interval' => 'int',
        // 'visibility' => 'bool',
        'archived' => 'bool',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'task_workers')->withTimestamps();
    }
    public function taskOccurrences()
    {
        return $this->hasMany(TaskOccurrence::class);
    }

    /**
     * Latest visible occurrence for quick access (by created_at).
     */
    public function latestOccurrence()
    {
        return $this->hasOne(TaskOccurrence::class)
            ->where('visibility', '1')
            ->latestOfMany('created_at');
    }

    /**
     * Latest occurrence regardless of visibility (admin use).
     */
    public function latestOccurrenceWithoutVisibility()
    {
        return $this->hasOne(TaskOccurrence::class)
            ->latestOfMany('created_at');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
