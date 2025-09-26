<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_tariffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('price_per_lesson', 10, 2);
            $table->string('currency', 3)->default('UAH');
            $table->date('starts_on');
            $table->date('ends_on')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'starts_on']);
        });

        Schema::create('student_monthly_statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('year');
            $table->unsignedTinyInteger('month');
            $table->unsignedInteger('lessons_count')->default(0);
            $table->decimal('amount_due', 10, 2)->default(0);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_monthly_statements');
        Schema::dropIfExists('student_tariffs');
    }
};
