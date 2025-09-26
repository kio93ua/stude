<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'test_id',
        'position',
        'category',
        'type',
        'prompt',
        'rich_content',
        'attachment_path',
        'points',
    ];

    protected $casts = [
        'points' => 'integer',
        'position' => 'integer',
    ];

    public function test(): BelongsTo
    {
        return $this->belongsTo(StudyTest::class, 'test_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(TestQuestionOption::class, 'question_id')->orderBy('position');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(TestAnswer::class, 'question_id');
    }
}
