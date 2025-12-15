<?php

namespace App\Models;

use App\Models\Traits\HasVisibilityScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskOccurrence extends Model
{
    use HasFactory;
    use HasVisibilityScope;



    protected $fillable = [
        "task_id",
        "branch_id_snapshot",
        "branch_name_snapshot",
        "service_id_snapshot",
        "service_name_snapshot",
        "due_date",
        "status_id",
        "start_date",
        "end_date",
        "requires_document",
        "document_path",
        "payment_status",
        "visibility",
    ];
    protected $casts = [
        "due_date" => "date",
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'requires_document' => 'bool',
        'visibility' => 'string',
    ];



    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    public function status()
    {
        return $this->belongsTo(TaskOccurrenceStatus::class);
    }
    public function workers()
    {
        return $this->hasMany(TaskOccurrenceWorker::class);
    }

    /**
     * Determine if this occurrence is the latest for its task.
     */
    public function isLatest(): bool
    {
        $latestId = $this->task?->latestOccurrence?->id;
        return $latestId !== null && (int) $latestId === (int) $this->id;
    }
}
