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
        Schema::create('ftth_fiber_cables', function (Blueprint $table) {

            $table->id();

            $table->string('info')->nullable();

            $table->string('fiber_identification');

            $table->foreignId('fiber_box_id')
                ->constrained('ftth_fiber_boxes');

            $table->decimal('optical_power', 8, 2)->nullable();

            $table->foreignId('cable_fiber_box_id')
                ->nullable()
                ->constrained('ftth_cable_fiber_boxes')
                ->nullOnDelete();

            $table->unsignedBigInteger('splinter_id')->nullable();
            $table->unsignedBigInteger('splinter_out_id')->nullable();


            $table->enum('cable_fiber_box_direction', [
                'input',
                'output'
            ])->nullable();

            $table->enum('status', [
                'unused',
                'used'
            ])->nullable();


            $table->timestamps();

            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ftth_fiber_cable');
    }
};
