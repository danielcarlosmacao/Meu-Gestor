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
        Schema::create('service_maintenances', function (Blueprint $table) {
            $table->id();
            $table->date('date_maintenance');
            $table->foreignId('service_client_id')->constrained('service_clients')->onDelete('cascade');
            $table->string('maintenance');
            $table->decimal('cost_enterprise', 10, 2)->default(0);
            $table->decimal('cost_client', 10, 2)->default(0);
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
