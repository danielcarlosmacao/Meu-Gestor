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
        Schema::create('batterys', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->string('mark')->nullable(false);
            $table->integer('amps')->nullable(false);
            $table->timestamps();
            $table->softDeletes(); // adiciona coluna "deleted_at"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batterys');
    }
};
