<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('task_occurrence_workers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_occurrence_id')->nullable()
                ->constrained('task_occurrences')
                ->cascadeOnDelete();

            // Worker snapshot ( not connected to workers table just storing the name and id)
            $table->unsignedBigInteger('worker_id_snapshot')->nullable();
            $table->string('worker_name_snapshot')->nullable();

            $table->timestamps();

            $table->index('task_occurrence_id');
            $table->index('worker_id_snapshot');
            $table->unique(['task_occurrence_id', 'worker_id_snapshot'], 'task_occ_worker_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_occurrence_workers');
    }
};
