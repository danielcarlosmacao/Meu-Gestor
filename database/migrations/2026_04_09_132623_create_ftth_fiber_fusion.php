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
        Schema::create('ftth_fiber_fusion', function (Blueprint $table) {
            $table->id();
            $table->string('info')->nullable();
            $table->foreignId('fiber_box_id')
                ->constrained('ftth_fiber_boxes')
                ->cascadeOnDelete();
            $table->foreignId('fiber_cables_id_1')
                ->constrained('ftth_fiber_cables')
                ->cascadeOnDelete();
            $table->foreignId('fiber_cables_id_2')
                ->constrained('ftth_fiber_cables')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ftth_fiber_fusion');
    }
};
