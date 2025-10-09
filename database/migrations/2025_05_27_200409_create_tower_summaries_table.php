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
        Schema::create('tower_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tower_id')->constrained('towers');
            $table->decimal('consumption_ah_day',18,2)->nullable(false);
            $table->decimal('time_ah_consumption',18,2)->nullable(false);
            $table->decimal('battery_required',18,2)->nullable(false);
            $table->decimal('watts_plate',18,2)->nullable(false);
            $table->decimal('amps_plate',18,2)->nullable(false);
            $table->timestamps();
            $table->softDeletes(); // adiciona coluna "deleted_at"

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tower_summary');
    }
};
