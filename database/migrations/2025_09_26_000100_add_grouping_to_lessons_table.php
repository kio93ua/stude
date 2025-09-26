<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            if (! Schema::hasColumn('lessons', 'group_id')) {
                $table->foreignId('group_id')->nullable()->after('series_id')->constrained('study_groups')->nullOnDelete();
            }

            if (! Schema::hasColumn('lessons', 'format')) {
                $table->string('format')->default('individual')->after('group_id');
            }

            if (! Schema::hasColumn('lessons', 'is_temporary')) {
                $table->boolean('is_temporary')->default(false)->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            if (Schema::hasColumn('lessons', 'is_temporary')) {
                $table->dropColumn('is_temporary');
            }

            if (Schema::hasColumn('lessons', 'format')) {
                $table->dropColumn('format');
            }

            if (Schema::hasColumn('lessons', 'group_id')) {
                $table->dropConstrainedForeignId('group_id');
            }
        });
    }
};
