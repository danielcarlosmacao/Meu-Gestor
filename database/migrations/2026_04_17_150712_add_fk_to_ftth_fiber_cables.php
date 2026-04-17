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
        Schema::table('ftth_fiber_cables', function (Blueprint $table) {
            $table->foreign('splinter_id')
                ->references('id')
                ->on('ftth_splinters')
                ->nullOnDelete();

            $table->foreign('splinter_out_id')
                ->references('id')
                ->on('ftth_splinters')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ftth_fiber_cables', function (Blueprint $table) {
            //
        });
    }
};
