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
        Schema::create('workshops', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Exemplo: "troca de óleo", "alinhamento"
            $table->string('info'); // Exemplo: "troca de óleo", "alinhamento"
            $table->enum('vehicle_type', ['car', 'motorcycle', 'truck', 'others', 'all']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
