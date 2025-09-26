<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'requested_by_id',
        'handled_by_id',
        'type',
        'status',
        'proposed_starts_at',
        'proposed_duration',
        'payload',
        'comment',
        'response_comment',
        'handled_at',
    ];

    protected $casts = [
        'proposed_starts_at' => 'datetime',
        'handled_at' => 'datetime',
        'payload' => 'array',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_id');
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by_id');
    }
}
