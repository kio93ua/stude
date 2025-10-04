<?php

namespace App\Models;

use App\Models\Concerns\OptimizesPostMedia;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;
    use OptimizesPostMedia;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_image_path',
        'gallery_images',
        'youtube_url',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Post $post): void {
            $post->title = trim($post->title);
            $post->excerpt = $post->excerpt ? trim($post->excerpt) : null;
            $post->youtube_url = $post->youtube_url ? trim($post->youtube_url) : null;

            $requestedSlug = $post->slug ?: Str::slug($post->title);
            $post->slug = static::generateUniqueSlug($requestedSlug, $post->id);

            if ($post->gallery_images !== null) {
                $post->gallery_images = array_values(array_filter(
                    array_map(
                        fn ($value) => is_string($value) ? trim($value) : null,
                        (array) $post->gallery_images
                    ),
                    fn ($value) => ! empty($value)
                )) ?: null;
            }

            if ($post->is_published) {
                $post->published_at ??= now();
            } elseif ($post->isDirty('is_published')) {
                $post->published_at = null;
            }
        });
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->whereNotNull('published_at');
    }

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        $url = $this->youtube_url;

        if (! is_string($url) || $url === '') {
            return null;
        }

        $url = trim($url);

        $patterns = [
            '~youtu\.be/([\w-]{11})~i',
            '~youtube\.com/(?:watch\?v=|embed/|v/|shorts/)([\w-]{11})~i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return 'https://www.youtube.com/embed/' . $matches[1];
            }
        }

        $queryString = parse_url($url, PHP_URL_QUERY);
        if ($queryString) {
            parse_str($queryString, $params);
            if (! empty($params['v']) && preg_match('~^[\w-]{11}$~', $params['v'])) {
                return 'https://www.youtube.com/embed/' . $params['v'];
            }
        }

        return null;
    }

    protected static function generateUniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $slug = Str::slug($slug);

        if ($slug === '') {
            $slug = (string) Str::uuid();
        }

        $baseSlug = $slug;
        $counter = 1;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
