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
        'document',
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

    /**
     * Returns the recent tasks with their associated users, status, service and branch company.
     * The tasks are filtered to only include completed, in_progress and pending tasks.
     * The results are ordered in descending order by start date and limited to the specified count.
     */
    public function scopeRecentWithRelations($query, $limit = 5)
    {
        return $query->whereHas('status', function ($q) {
            $q->whereIn('name', ['completed', 'in_progress', 'pending']);
        })
            ->with(['users', 'status', 'service', 'branch.company'])
            ->orderByDesc('start_date')
            ->take($limit);
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
