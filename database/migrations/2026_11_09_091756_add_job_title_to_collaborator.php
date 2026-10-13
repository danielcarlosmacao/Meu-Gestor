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
        Schema::table('collaborators', function (Blueprint $table) {
            $table->foreignId('job_title_id')->nullable()->after('admission_date')->constrained('collaborator_jobtitles');
            $table->string('work_scale')->nullable()->after('job_title_id');
            $table->foreignId('user_id')->nullable()->after('admission_date')->constrained('users');
            $table->string('contact')->nullable()->after('name');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collaborators', function (Blueprint $table) {
            //
        });
    }
};
