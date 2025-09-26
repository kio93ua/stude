<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lesson_enrollments', function (Blueprint $table) {
            if (! Schema::hasColumn('lesson_enrollments', 'status')) {
                $table->string('status')->default('scheduled')->after('student_id');
            }

            $table->timestamp('status_changed_at')->nullable()->after('status');
            $table->timestamp('attended_at')->nullable()->after('status_changed_at');
            $table->boolean('is_billable')->default(true)->after('attended_at');
        });
    }

    public function down(): void
    {
        Schema::table('lesson_enrollments', function (Blueprint $table) {
            $table->dropColumn(['status_changed_at', 'attended_at', 'is_billable']);
        });
    }
};
