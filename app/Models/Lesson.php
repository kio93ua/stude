<?php

namespace App\Models;

use App\Models\LessonSeries;
use App\Models\LessonChangeRequest;
use App\Models\StudyGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'series_id',
        'title',
        'topic',
        'description',
        'starts_at',
        'scheduled_for',
        'duration_minutes',
        'meeting_url',
        'timezone',
        'status',
        'cover_image_path',
        'group_id',
        'format',
        'is_temporary',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'scheduled_for' => 'date',
        'duration_minutes' => 'integer',
        'is_temporary' => 'boolean',
    ];

    public function series(): BelongsTo
    {
        return $this->belongsTo(LessonSeries::class, 'series_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(StudyGroup::class, 'group_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(LessonMaterial::class);
    }

    public function tests(): HasMany
    {
        return $this->hasMany(StudyTest::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(LessonEnrollment::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'lesson_enrollments', 'lesson_id', 'student_id')
            ->withPivot(['status', 'status_changed_at', 'attended_at', 'is_billable', 'progress_percent', 'notes'])
            ->withTimestamps();
    }

    public function changeRequests(): HasMany
    {
        return $this->hasMany(LessonChangeRequest::class);
    }
}
