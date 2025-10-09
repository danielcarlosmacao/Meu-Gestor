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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('license_plate'); // Placa
            $table->string('brand');         // Marca
            $table->string('model');         // Modelo
            $table->enum('type', ['car', 'motorcycle', 'truck', 'others']); // Tipo do veículo
            $table->year('year');            // Ano
            $table->string('fuel_type');     // Combustível
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->timestamps();
            $table->softDeletes();           // Suporte a soft deletes (deleted_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
