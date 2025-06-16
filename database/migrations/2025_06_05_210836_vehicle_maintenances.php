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
        Schema::create('vehicle_maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained();
            $table->enum('type', ['preventive', 'corrective']);
            $table->date('maintenance_date');
            $table->decimal('cost', 10, 2)->nullable();
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->integer('mileage')->nullable();
            $table->text('parts_used')->nullable();
            $table->text('workshop')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
