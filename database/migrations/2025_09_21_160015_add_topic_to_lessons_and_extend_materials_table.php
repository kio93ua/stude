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
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('topic')->nullable()->after('title');
        });

        Schema::table('lesson_materials', function (Blueprint $table) {
            $table->string('external_url')->nullable()->after('file_url');
            $table->longText('content')->nullable()->after('thumbnail_path');
        });

        DB::statement('ALTER TABLE lesson_materials MODIFY file_url VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE lesson_materials MODIFY file_url VARCHAR(255) NOT NULL');

        Schema::table('lesson_materials', function (Blueprint $table) {
            $table->dropColumn(['external_url', 'content']);
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('topic');
        });
    }
};
