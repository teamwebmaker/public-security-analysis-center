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

        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->unsignedSmallInteger('economic_activity_type_id')->nullable();
            $table->foreign('economic_activity_type_id')->references('id')->on('economic_activity_types')->onDelete('set null');
            $table->bigInteger('economic_activity_code');
            $table->boolean('high_risk_activities');
            $table->enum('risk_level', [
                'extremely high',
                'very high',
                'high',
                'medium',
                'low'
            ])->default('low risk');
            $table->boolean('evacuation_plan');

            $table->string('identification_code', 50)->unique();
            $table->enum('visibility', ['0', '1'])->default('1');
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
