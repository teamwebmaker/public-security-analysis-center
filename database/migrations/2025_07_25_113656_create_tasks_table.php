<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create("tasks", function (Blueprint $table) {
            $table->id();

            // $table->unsignedTinyInteger("status_id");
            // $table
            //     ->foreign("status_id", "tasks_status_id_foreign")
            //     ->references("id")
            //     ->on("task_statuses")
            //     ->onDelete("restrict");

            // $table->dateTime('start_date')->nullable();
            // $table->dateTime('end_date')->nullable();

            $table
                ->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->onDelete('set null')
                ->index();
            // snapshot of branch name in case branch is deleted/renamed
            $table->string("branch_name_snapshot")->nullable();

            $table
                ->foreignId("service_id")
                ->nullable()
                ->constrained("services")
                ->name("tasks_service_id_foreign")
                ->onDelete('set null')
                ->index();
            // snapshot of service name in case service is deleted/renamed
            $table->string("service_name_snapshot")->nullable();

            $table->integer("recurrence_interval")->nullable();
            $table->boolean('is_recurring')->default(false);

            $table->enum("archived", ["0", "1"])->default("0");
            $table->enum("visibility", ["0", "1"])->default("1");
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("tasks");
    }
};
