<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('primary_student_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('weekday')->comment('0 (Mon) .. 6 (Sun)');
            $table->time('starts_at');
            $table->unsignedInteger('duration_minutes')->default(60);
            $table->date('starts_on');
            $table->date('ends_on')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->foreignId('series_id')->nullable()->after('teacher_id')->constrained('lesson_series')->nullOnDelete();
            $table->date('scheduled_for')->nullable()->after('series_id');
            $table->string('timezone')->nullable()->after('duration_minutes');
        });

        DB::table('lessons')->select('id', 'starts_at')->chunkById(100, function ($lessons) {
            foreach ($lessons as $lesson) {
                if (! $lesson->starts_at) {
                    continue;
                }

                DB::table('lessons')->where('id', $lesson->id)->update([
                    'scheduled_for' => \Illuminate\Support\Carbon::parse($lesson->starts_at)->format('Y-m-d'),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('scheduled_for');
            $table->dropColumn('timezone');
            $table->dropConstrainedForeignId('series_id');
        });

        Schema::dropIfExists('lesson_series');
    }
};
