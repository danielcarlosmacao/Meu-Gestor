<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ftth_cable_fiber_boxes', function (Blueprint $table) {

            $table->id();

            $table->string('info')->nullable();

            $table->string('color')->nullable();

            $table->integer('number_fiber');

            $table->foreignId('input_fiber_box_id')
                ->constrained('ftth_fiber_boxes')
                ->cascadeOnDelete();

            $table->foreignId('output_fiber_box_id')
                ->nullable()
                ->constrained('ftth_fiber_boxes')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ftth_cable_fiber_box');
    }
};
