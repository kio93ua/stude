<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vocabulary_entries')) {
            return;
        }

        Schema::create('vocabulary_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('term');
            $table->string('translation');
            $table->text('definition')->nullable();
            $table->text('example')->nullable();
            $table->foreignId('last_updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['student_id', 'term']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocabulary_entries');
    }
};
