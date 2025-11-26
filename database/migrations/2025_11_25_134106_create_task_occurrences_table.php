<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('task_occurrences', function (Blueprint $table) {
            $table->id();

            // Also have connection with worker one to many

            // Link to tasks
            $table->foreignId('task_id')
                ->constrained()
                ->cascadeOnDelete();

            // Branch snapshot
            $table->unsignedBigInteger('branch_id_snapshot')->nullable();
            $table->string('branch_name_snapshot');

            // Service snapshot
            $table->unsignedBigInteger('service_id_snapshot')->nullable();
            $table->string('service_name_snapshot');

            // Recurrence date (which cycle this belongs to)
            $table->date('scheduled_for');

            // Connection with task_occurrence_statuses
            $table->unsignedTinyInteger("status_id");
            $table
                ->foreign("status_id", "task_occurrences_status_id_foreign")
                ->references("id")
                ->on("task_occurrence_statuses")
                ->onDelete("restrict");

            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();

            // Document required on completion
            $table->boolean('requires_document')->default(true);
            $table->text('document_path')->nullable();


            $table->enum('payment_status', ['paid', 'unpaid', 'pending', 'overdue'])->default('unpaid');
            $table->enum("visibility", ["0", "1"])->default("1");

            $table->timestamps();

            // indexes
            $table->index('task_id');
            $table->index('status_id');
            $table->index('scheduled_for');
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_occurrences');
    }
};
