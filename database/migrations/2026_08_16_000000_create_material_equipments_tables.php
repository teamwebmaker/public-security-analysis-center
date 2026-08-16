<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('material_equipments')) {
            Schema::create('material_equipments', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
                $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
                $table->string('document_path');
                $table->string('document_original_name');
                $table->string('document_mime_type')->nullable();
                $table->string('document_visibility', 20)->default('private');
                $table->timestamps();

                $table->index(['branch_id', 'created_at']);
                $table->index(['created_by_user_id', 'created_at']);
            });
        }

        $uniqueIndex = 'material_equipment_signatures_equipment_user_unique';
        $signedIndex = 'material_equipment_signatures_user_signed_index';

        if (! Schema::hasTable('material_equipment_signatures')) {
            Schema::create('material_equipment_signatures', function (Blueprint $table) use ($uniqueIndex, $signedIndex) {
                $table->id();
                $table->foreignId('material_equipment_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamp('signed_at');
                $table->timestamps();

                $table->unique(['material_equipment_id', 'user_id'], $uniqueIndex);
                $table->index(['user_id', 'signed_at'], $signedIndex);
            });

            return;
        }

        if (! Schema::hasIndex('material_equipment_signatures', $uniqueIndex)) {
            Schema::table('material_equipment_signatures', function (Blueprint $table) use ($uniqueIndex) {
                $table->unique(['material_equipment_id', 'user_id'], $uniqueIndex);
            });
        }

        if (! Schema::hasIndex('material_equipment_signatures', $signedIndex)) {
            Schema::table('material_equipment_signatures', function (Blueprint $table) use ($signedIndex) {
                $table->index(['user_id', 'signed_at'], $signedIndex);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('material_equipment_signatures');
        Schema::dropIfExists('material_equipments');
    }
};
