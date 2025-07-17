<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name', 50)->unique();
            $table->string('display_name', 50);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
