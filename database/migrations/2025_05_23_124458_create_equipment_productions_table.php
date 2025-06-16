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
        Schema::create('equipment_productions', function (Blueprint $table) {
            $table->id();
            $table->string('identification')->nullable();
            $table->foreignId('tower_id')->constrained('towers');
            $table->foreignId('equipment_id')->constrained('equipments');
            $table->enum('active',['yes','no'])->default('yes');
            $table->timestamps();
            $table->softDeletes(); // adiciona coluna "deleted_at"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipmentproductions');
    }
};
