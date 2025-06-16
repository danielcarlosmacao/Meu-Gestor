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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tower_id')->constrained('towers');
            $table->string('info');
            $table->date('maintenance_date');
            $table->date('next_maintenance_date');
            $table->enum('status',['pending','completed','archived'])->default('pending');
            $table->timestamps();
            $table->softDeletes(); // adiciona coluna "deleted_at"
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
