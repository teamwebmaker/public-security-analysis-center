<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('surname');
            $table->string('position');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('id_number')->nullable();
            $table->boolean('personal_details_visible')->default(false);
            $table->timestamps();

            $table->index(['company_id', 'surname', 'name']);
        });

        Schema::create('employee_branch', function (Blueprint $table) {
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['employee_id', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_branch');
        Schema::dropIfExists('employees');
    }
};
