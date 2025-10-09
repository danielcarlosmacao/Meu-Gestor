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
        Schema::create('vehicle_maintenance_vehicle_service', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('vehicle_maintenance_id');
            $table->unsignedBigInteger('vehicle_service_id');

            // Foreign keys com nomes curtos
            $table->foreign('vehicle_maintenance_id', 'fk_vm_vs_maintenance')
                  ->references('id')->on('vehicle_maintenances');

            $table->foreign('vehicle_service_id', 'fk_vm_vs_service')
                  ->references('id')->on('vehicle_services');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_maintenance_vehicle_service');
    }
};
