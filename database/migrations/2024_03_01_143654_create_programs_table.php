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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('description');
            $table->string('image')->nullable();
            $table->string('certificate_image')->nullable();
            $table->string('video');
            $table->string('price');
            $table->date('start_date');
            $table->date('end_date');
            $table->json('days');
            $table->json('hour');
            $table->string('duration');
            $table->string('address')->nullable();
            $table->enum('visibility', ['0', '1'])->default('1');
            $table->tinyInteger('sortable')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
