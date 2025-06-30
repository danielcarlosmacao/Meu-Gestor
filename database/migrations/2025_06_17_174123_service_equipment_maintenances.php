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
        Schema::create('service_equipment_maintenances', function (Blueprint $table) {
            $table->id();
            $table->date('date_maintenance');
            $table->string('assistance')->nullable();
            $table->foreignId('service_client_id')->constrained('service_clients')->onDelete('cascade');
            $table->string('equipment');
            $table->string('erro')->nullable();
            $table->date('date_send')->nullable();
            $table->date('date_received')->nullable();
            $table->text('solution')->nullable();
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
