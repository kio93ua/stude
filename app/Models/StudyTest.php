<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'lesson_id',
        'title',
        'category',
        'description',
        'link_url',
        'max_score',
        'available_from',
        'due_at',
        'instructions',
    ];

    protected $casts = [
        'available_from' => 'datetime',
        'due_at' => 'datetime',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(TestResult::class, 'test_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(TestQuestion::class, 'test_id')->orderBy('position');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class, 'test_id');
    }
}
