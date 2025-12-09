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
    ];



    /**
     * Returns task statuses that are eligible for worker assignment.
     * In other words, This method defines which task statuses are eligible for assignment or display
     * when a user is being associated with tasks. 
    //  * @return string[]
     */
    // public static function workerAssignableTaskStatuses(): array
    // {
    //     return ['pending', 'in_progress', 'on_hold'];
    // }

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
}
