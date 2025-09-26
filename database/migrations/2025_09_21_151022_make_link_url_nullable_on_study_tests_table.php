<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('study_tests', function (Blueprint $table) {
            // Laravel's schema builder requires doctrine/dbal for column modifications.
        });

        DB::statement('ALTER TABLE study_tests MODIFY link_url VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('study_tests', function (Blueprint $table) {
            //
        });

        DB::statement('ALTER TABLE study_tests MODIFY link_url VARCHAR(255) NOT NULL');
    }
};
