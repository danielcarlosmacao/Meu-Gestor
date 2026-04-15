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
        Schema::create('ftth_splinter_losses', function (Blueprint $table) {

            $table->id();
            $table->string('type');
            $table->integer('derivations')->default(2);
            $table->enum('splinter_type', [
                'balanced',
                'unbalanced'
            ]);
            $table->decimal('loss1', 8, 2);
            $table->decimal('loss2', 8, 2)->nullable();

            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ftth_splinter_loss');
    }
};
