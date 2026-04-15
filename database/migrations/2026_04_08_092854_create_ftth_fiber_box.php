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
        Schema::create('ftth_fiber_boxes', function (Blueprint $table) {

            $table->id();

            $table->string('number');

            $table->text('info')->nullable();

            $table->foreignId('pon_id')
                ->constrained('ftth_pons')
                ->cascadeOnDelete();

            $table->string('coordinates')->nullable();

            $table->timestamps();

            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ftth_fiber_box');
    }
};
