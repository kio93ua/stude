<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('primary_teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('study_group_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('study_groups')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['group_id', 'student_id']);
        });

        Schema::create('study_group_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('study_groups')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['group_id', 'teacher_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_group_teacher');
        Schema::dropIfExists('study_group_student');
        Schema::dropIfExists('study_groups');
    }
};
