<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class StudyGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'primary_teacher_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $group): void {
            DB::table('study_group_teacher')
                ->where('group_id', $group->id)
                ->update(['is_primary' => false]);

            if ($group->primary_teacher_id) {
                $group->teachers()->syncWithoutDetaching([
                    $group->primary_teacher_id => ['is_primary' => true],
                ]);

                DB::table('study_group_teacher')
                    ->where('group_id', $group->id)
                    ->where('teacher_id', $group->primary_teacher_id)
                    ->update(['is_primary' => true]);
            }

            $group->students()
                ->wherePivotNull('joined_at')
                ->get()
                ->each(function (User $student) use ($group): void {
                    $group->students()->updateExistingPivot($student->id, ['joined_at' => now()]);
                });
        });
    }

    public function primaryTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'primary_teacher_id');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'study_group_teacher', 'group_id', 'teacher_id')
            ->withPivot(['is_primary'])
            ->withTimestamps();
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'study_group_student', 'group_id', 'student_id')
            ->withPivot(['joined_at'])
            ->withTimestamps();
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'group_id');
    }
}
