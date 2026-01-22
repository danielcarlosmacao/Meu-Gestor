<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('whatsapp_logs', function (Blueprint $table) {

            // 1️⃣ Remove a foreign key
            if (Schema::hasColumn('whatsapp_logs', 'maintenance_id')) {
                $table->dropForeign(['maintenance_id']);
                $table->dropColumn('maintenance_id');
            }

            // 2️⃣ Campos polimórficos
            $table->string('ref_type')->after('recipient_id');
            $table->unsignedBigInteger('ref_id')->after('ref_type');

            // 3️⃣ Índice para performance
            $table->index(['ref_type', 'ref_id']);
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_logs', function (Blueprint $table) {

            // Remove polimórfico
            $table->dropIndex(['ref_type', 'ref_id']);
            $table->dropColumn(['ref_type', 'ref_id']);

            // Restaura maintenance_id
            $table->foreignId('maintenance_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
        });
    }
};
