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
    Schema::table('batterys', function (Blueprint $table) {
        $table->string('type')->nullable()->default(null)->after('mark');
        $table->integer('voltage')->nullable()->default('12')->after('type');
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
