<?php

namespace App\Models;

use App\Enums\HomeworkStatus;
use App\Models\Concerns\ManagesContentMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeworkSubmission extends Model
{
    use HasFactory;
    use ManagesContentMedia;

    protected $fillable = [
        'homework_id',
        'student_id',
        'body',
        'images',
        'attachments',
        'status',
        'teacher_feedback',
        'feedback_left_at',
        'submitted_at',
    ];

    protected $casts = [
        'images' => 'array',
        'attachments' => 'array',
        'status' => HomeworkStatus::class,
        'feedback_left_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saved(function () {
            static::where('submitted_at', '<', now()->subMonth())->delete();
        });
    }

    public function homework(): BelongsTo
    {
        return $this->belongsTo(Homework::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function statusLabel(): string
    {
        return $this->status?->label() ?? '—';
    }
}
