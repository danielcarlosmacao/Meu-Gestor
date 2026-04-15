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
        Schema::create('ftth_splinters', function (Blueprint $table) {

            $table->id();

            $table->string('name');

            $table->enum('type', ['client', 'network']);

            $table->foreignId('fiber_box_id')
                ->constrained('ftth_fiber_boxes')
                ->cascadeOnDelete();

            $table->foreignId('splinter_input')
                ->constrained('ftth_fiber_cables')
                ->cascadeOnDelete();

            $table->foreignId('splinter')
                ->constrained('ftth_splinter_losses')
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
        Schema::dropIfExists('ftth_ftth_splinter');
    }
};
