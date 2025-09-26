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
        Schema::create('test_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('test_questions')->cascadeOnDelete();
            $table->unsignedInteger('position')->default(1);
            $table->text('label');
            $table->string('attachment_path')->nullable();
            $table->boolean('is_correct')->default(false);
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_question_options');
    }
};
