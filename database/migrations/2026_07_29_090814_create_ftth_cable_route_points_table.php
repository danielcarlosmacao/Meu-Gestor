<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ftth_cable_route_points', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cable_fiber_box_id')
                ->constrained('ftth_cable_fiber_boxes')
                ->cascadeOnDelete();

            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            $table->unsignedInteger('position')->default(0);

            $table->timestamps();

            $table->index([
                'cable_fiber_box_id',
                'position'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ftth_cable_route_points');
    }
};