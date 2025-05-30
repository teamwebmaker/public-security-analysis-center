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
            $table->string('video')->nullable();
            $table->string('price')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->json('days')->nullable();
            $table->json('hour')->nullable();
            $table->string('duration')->nullable();
            $table->string('address')->nullable();
            $table->unsignedBigInteger('mentor_id')->nullable();
            $table->foreign('mentor_id')->references('id')->on('mentors')->onDelete('cascade');
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
