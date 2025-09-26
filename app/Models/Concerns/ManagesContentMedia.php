<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ManagesContentMedia
{
    protected array $originalImages = [];

    public static function bootManagesContentMedia(): void
    {
        static::saving(function ($model) {
            $model->originalImages = collect($model->getOriginal('images') ?? [])->all();

            $hasVideosAttribute = method_exists($model, 'isFillable') && $model->isFillable('videos');

            if ($hasVideosAttribute || array_key_exists('videos', $model->getAttributes())) {
                $legacyUrl = $model->getAttribute('external_url') ?? null;
                $model->videos = $model->normalizeVideos($model->videos ?? [], $legacyUrl);

                if (($model->isFillable('external_url') ?? false) || array_key_exists('external_url', $model->getAttributes())) {
                    $model->external_url = $model->videos[0]['url'] ?? $legacyUrl;
                }
            }

            $model->images = $model->optimizeImages($model->images ?? []);
        });

        static::saved(function ($model) {
            $model->deleteObsoleteImages();
        });

        static::deleted(function ($model) {
            $model->deleteImageFiles($model->images ?? []);
            $model->deleteImageFiles($model->attachments ?? []);

            if (($model->isFillable('file_url') ?? false) && ! empty($model->file_url)) {
                Storage::disk('public')->delete($model->file_url);
            }
        });
    }

    protected function optimizeImages(array $paths): array
    {
        return collect($paths)
            ->filter()
            ->take(3)
            ->map(fn (string $path) => $this->optimizeImage($path))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function optimizeImage(string $path): ?string
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($path) || ! function_exists('imagewebp')) {
            return $path;
        }

        $absolute = $disk->path($path);
        $extension = strtolower(pathinfo($absolute, PATHINFO_EXTENSION));

        if ($extension === 'webp') {
            $this->ensureWebpUnderLimit($absolute);

            return $path;
        }

        $image = $this->createImageResource($absolute, $extension);

        if (! $image) {
            return $path;
        }

        $newPath = 'materials/images/' . Str::uuid()->toString() . '.webp';

        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        if (! imagewebp($image, $disk->path($newPath), 75)) {
            imagedestroy($image);

            return $path;
        }

        imagedestroy($image);

        $disk->delete($path);

        $this->ensureWebpUnderLimit($disk->path($newPath));

        return $newPath;
    }

    protected function createImageResource(string $absolutePath, string $extension)
    {
        return match ($extension) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($absolutePath),
            'png' => @imagecreatefrompng($absolutePath),
            'gif' => @imagecreatefromgif($absolutePath),
            default => @imagecreatefromstring(@file_get_contents($absolutePath)),
        };
    }

    protected function ensureWebpUnderLimit(string $absolutePath): void
    {
        if (! function_exists('imagecreatefromwebp') || ! file_exists($absolutePath)) {
            return;
        }

        $maxBytes = 3 * 1024 * 1024;

        if (filesize($absolutePath) <= $maxBytes) {
            return;
        }

        $image = @imagecreatefromwebp($absolutePath);

        if (! $image) {
            return;
        }

        imagewebp($image, $absolutePath, 70);
        imagedestroy($image);

        if (filesize($absolutePath) > $maxBytes) {
            $image = @imagecreatefromwebp($absolutePath);
            if ($image) {
                imagewebp($image, $absolutePath, 60);
                imagedestroy($image);
            }
        }
    }

    protected function deleteObsoleteImages(): void
    {
        $disk = Storage::disk('public');

        $current = collect($this->images ?? []);
        $original = collect($this->originalImages ?? []);

        $original->diff($current)->each(fn (string $path) => $disk->delete($path));
    }

    protected function deleteImageFiles(array $paths): void
    {
        $disk = Storage::disk('public');

        collect($paths)->filter()->each(fn (string $path) => $disk->delete($path));
    }

    protected function normalizeVideos(array $videos, ?string $legacyUrl = null): array
    {
        $normalized = collect($videos)
            ->filter(fn ($entry) => is_array($entry) && filled($entry['url'] ?? null))
            ->map(function (array $entry) {
                $url = trim($entry['url']);

                return [
                    'label' => filled($entry['label'] ?? null) ? trim($entry['label']) : null,
                    'url' => $url,
                    'note' => filled($entry['note'] ?? null) ? trim($entry['note']) : null,
                ];
            })
            ->values();

        if ($normalized->isEmpty() && filled($legacyUrl)) {
            $normalized->push([
                'label' => null,
                'url' => trim($legacyUrl),
                'note' => null,
            ]);
        }

        return $normalized->all();
    }
}
