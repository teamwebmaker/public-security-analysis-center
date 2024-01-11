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
        Schema::create('main_menus', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->string('link');
            $table->enum('target', ['_self', '_blank'])-> default('_self');
            $table->enum('type', ['default', 'absolute', 'dropdown'])-> default('default');
            $table->tinyInteger('sorted') -> default(1) -> unsigned();
            $table->enum('visibility', ['0', '1']) -> default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('main_menus');
    }
};
