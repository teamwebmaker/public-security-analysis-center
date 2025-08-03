<?php

namespace App\Models;

use App\Models\Traits\HasVisibilityScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    use HasVisibilityScope;

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];
    protected $fillable = [
        'service_id',
        'status_id',
        'branch_id',
        'branch_name',
        'service_name',
        'start_date',
        'end_date',
        'archived',
        'visibility',
    ];

    /**
     * Returns task statuses that are eligible for worker assignment.
     * In other words, This method defines which task statuses are eligible for assignment or display
     * when a user is being associated with tasks. 
     * @return string[]
     */
    public static function workerAssignableTaskStatuses(): array
    {
        return ['pending', 'in_progress', 'on_hold'];
    }

    public function status()
    {
        return $this->belongsTo(TaskStatus::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'task_workers')->withTimestamps();
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
