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

            $table->unsignedTinyInteger("status_id");
            $table
                ->foreign("status_id", "tasks_status_id_foreign")
                ->references("id")
                ->on("task_statuses")
                ->onDelete("restrict");

            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();

            $table
                ->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->onDelete('set null')
                ->index();
            // if branch deleted, branch_name will be saved
            $table->string("branch_name")->nullable();
            $table
                ->foreignId("service_id")
                ->nullable()
                ->constrained("services")
                ->onDelete("set null")
                ->name("tasks_service_id_foreign")
                ->index();
            // if service deleted, service_name will be saved
            $table->string("service_name")->nullable();

            $table->enum("archived", ["0", "1"])->default("0");
            $table->enum("visibility", ["0", "1"])->default("1");
            $table->index(["start_date", "end_date"]);
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
