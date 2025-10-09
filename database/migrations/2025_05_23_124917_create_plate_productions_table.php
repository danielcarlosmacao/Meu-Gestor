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
        Schema::create('plate_productions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tower_id')->constrained('towers');
            $table->foreignId('plate_id')->constrained('plates');
            $table->date('installation_date')->nullable();
            $table->timestamps();
            $table->softDeletes(); // adiciona coluna "deleted_at"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plateproductions');
    }
};
