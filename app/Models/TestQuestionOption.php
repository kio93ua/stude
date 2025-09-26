<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestQuestionOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'position',
        'label',
        'attachment_path',
        'is_correct',
        'feedback',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'position' => 'integer',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(TestQuestion::class, 'question_id');
    }
}
