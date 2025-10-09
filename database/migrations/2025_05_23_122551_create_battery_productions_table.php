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
        Schema::create('battery_productions', function (Blueprint $table) {
            $table->id();
            $table->string('info')->nullable();
            $table->foreignId('tower_id')->constrained('towers');
            $table->foreignId('battery_id')->constrained('batterys'); 
            $table->integer('amount')->nullable(false);
            $table->date('installation_date')->nullable();
            $table->date('removal_date')->nullable();
            $table->enum('active',['yes','no'])->default('no');
            $table->decimal('production_percentage', 18,2)->nullable();
            $table->timestamps();
            $table->softDeletes(); // adiciona coluna "deleted_at"

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batteryproductions');
    }
};
