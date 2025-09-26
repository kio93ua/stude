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
        Schema::table('homework_submissions', function (Blueprint $table) {
            $table->string('status')->default('submitted')->after('attachments');
            $table->text('teacher_feedback')->nullable()->after('status');
            $table->dateTime('feedback_left_at')->nullable()->after('teacher_feedback');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('homework_submissions', function (Blueprint $table) {
            $table->dropColumn(['status', 'teacher_feedback', 'feedback_left_at']);
        });
    }
};
