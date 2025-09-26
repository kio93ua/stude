<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'student_id',
        'score',
        'status',
        'completed_at',
        'feedback',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(StudyTest::class, 'test_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
