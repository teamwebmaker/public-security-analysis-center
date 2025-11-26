<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskOccurrenceWorker extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_occurrence_id',
        'worker_id_snapshot',
        'worker_name_snapshot',
    ];

    public function taskOccurrence()
    {
        return $this->belongsTo(TaskOccurrence::class);
    }
}
