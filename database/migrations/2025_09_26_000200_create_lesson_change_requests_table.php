<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_change_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->foreignId('requested_by_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('handled_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['reschedule', 'cancel', 'assign_teacher', 'other'])->default('reschedule');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->dateTime('proposed_starts_at')->nullable();
            $table->unsignedInteger('proposed_duration')->nullable();
            $table->json('payload')->nullable();
            $table->text('comment')->nullable();
            $table->text('response_comment')->nullable();
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_change_requests');
    }
};
