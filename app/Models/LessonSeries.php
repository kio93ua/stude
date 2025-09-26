<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\StudyGroup;

class LessonSeries extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'primary_student_id',
        'group_id',
        'title',
        'description',
        'weekday',
        'starts_at',
        'duration_minutes',
        'starts_on',
        'ends_on',
        'is_active',
        'format',
    ];

    protected $casts = [
        'weekday' => 'integer',
        'starts_at' => 'datetime:H:i:s',
        'duration_minutes' => 'integer',
        'starts_on' => 'date',
        'ends_on' => 'date',
        'is_active' => 'boolean',
        'format' => 'string',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function primaryStudent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'primary_student_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(StudyGroup::class, 'group_id');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class, 'series_id');
    }
}
