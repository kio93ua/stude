<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'student_id',
        'result_id',
        'started_at',
        'submitted_at',
        'score',
        'max_score',
        'status',
        'meta',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'score' => 'integer',
        'max_score' => 'integer',
        'meta' => 'array',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(StudyTest::class, 'test_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function result(): BelongsTo
    {
        return $this->belongsTo(TestResult::class, 'result_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(TestAnswer::class, 'attempt_id');
    }
}
