<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskWorkerInvitation extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_DECLINED = 'declined';

    protected $fillable = [
        'task_id',
        'inviter_id',
        'invited_worker_id',
        'status',
        'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }

    public function invitedWorker()
    {
        return $this->belongsTo(User::class, 'invited_worker_id');
    }
}
