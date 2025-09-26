<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lesson_series', function (Blueprint $table) {
            if (! Schema::hasColumn('lesson_series', 'group_id')) {
                $table->foreignId('group_id')->nullable()->after('primary_student_id')->constrained('study_groups')->nullOnDelete();
            }

            if (! Schema::hasColumn('lesson_series', 'format')) {
                $table->string('format')->default('individual')->after('group_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lesson_series', function (Blueprint $table) {
            if (Schema::hasColumn('lesson_series', 'format')) {
                $table->dropColumn('format');
            }

            if (Schema::hasColumn('lesson_series', 'group_id')) {
                $table->dropConstrainedForeignId('group_id');
            }
        });
    }
};
