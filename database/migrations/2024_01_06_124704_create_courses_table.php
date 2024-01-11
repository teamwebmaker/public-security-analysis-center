<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('description');
            $table->string('image')->nullable();
            $table->string('video')->nullable();
            $table->json('duration');
            $table->json('days');
            $table->float('price');
            $table->json('location');
            $table->date('starting_date') -> nullable();
            $table->date('register_date') -> nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
