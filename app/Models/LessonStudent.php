<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'student_id',
        'status',
        'status_changed_at',
        'attended_at',
        'is_billable',
    ];

    protected $casts = [
        'status_changed_at' => 'datetime',
        'attended_at' => 'datetime',
        'is_billable' => 'boolean',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
