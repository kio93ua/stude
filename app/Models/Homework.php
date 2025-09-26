<?php

namespace App\Models;

use App\Models\Concerns\ManagesContentMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Homework extends Model
{
    use HasFactory;
    use ManagesContentMedia;

    protected $table = 'homeworks';

    protected $fillable = [
        'lesson_id',
        'teacher_id',
        'title',
        'description',
        'content',
        'videos',
        'resource_links',
        'attachments',
        'images',
        'due_at',
        'is_downloadable',
    ];

    protected $casts = [
        'videos' => 'array',
        'resource_links' => 'array',
        'attachments' => 'array',
        'images' => 'array',
        'due_at' => 'datetime',
        'is_downloadable' => 'boolean',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'homework_student', 'homework_id', 'student_id')
            ->withPivot(['status', 'submitted_at'])
            ->withTimestamps();
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(HomeworkSubmission::class);
    }
}
