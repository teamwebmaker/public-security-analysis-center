<?php

namespace App\Models;

use App\Models\Traits\HasVisibilityScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    use HasVisibilityScope;

    public const ACTIVE_OCCURRENCE_STATUSES = [
        'pending',
        'in_progress',
        'on_hold',
    ];


    protected $fillable = [
        'created_by_user_id',
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
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
    public function workerInvitations()
    {
        return $this->hasMany(TaskWorkerInvitation::class);
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

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('archived', '0')
            ->where('visibility', '1')
            ->whereHas('latestOccurrence.status', function (Builder $query) {
                $query->whereIn('name', self::ACTIVE_OCCURRENCE_STATUSES);
            });
    }

    public function isActive(): bool
    {
        $occurrence = $this->relationLoaded('latestOccurrence')
            ? $this->latestOccurrence
            : $this->latestOccurrence()->with('status')->first();

        if ($occurrence && !$occurrence->relationLoaded('status')) {
            $occurrence->load('status');
        }

        return !$this->archived
            && (string) $this->visibility === '1'
            && in_array($occurrence?->status?->name, self::ACTIVE_OCCURRENCE_STATUSES, true);
    }
}
