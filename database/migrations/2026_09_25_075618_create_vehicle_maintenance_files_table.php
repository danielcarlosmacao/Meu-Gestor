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
        Schema::create('vehicle_maintenance_files', function (Blueprint $table) {
            $table->id();
            $table->uuid('token')->unique();
            $table->foreignId('vehicle_maintenance_id')
                ->constrained('vehicle_maintenances')
                ->cascadeOnDelete();

            $table->string('original_name');
            $table->string('file_name');
            $table->string('path');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size')->nullable();
            $table->index('vehicle_maintenance_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_maintenance_files');
    }
};
