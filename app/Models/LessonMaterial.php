<?php

namespace App\Models;

use App\Models\Concerns\ManagesContentMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonMaterial extends Model
{
    use HasFactory;
    use ManagesContentMedia;

    protected $fillable = [
        'lesson_id',
        'title',
        'file_url',
        'external_url',
        'thumbnail_path',
        'content',
        'description',
        'resource_links',
        'attachments',
        'videos',
        'images',
        'is_downloadable',
    ];

    protected $casts = [
        'is_downloadable' => 'boolean',
        'resource_links' => 'array',
        'attachments' => 'array',
        'videos' => 'array',
        'images' => 'array',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
