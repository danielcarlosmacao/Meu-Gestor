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
        Schema::create('plates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->decimal('amps', 18,2)->nullable(false);
            $table->decimal('watts', 18,2)->nullable(false);
            $table->timestamps();
            $table->softDeletes(); // adiciona coluna "deleted_at"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plates');
    }
};
