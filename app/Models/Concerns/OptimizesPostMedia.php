<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait OptimizesPostMedia
{
    protected ?string $originalCoverImage = null;
    protected array $originalGalleryImages = [];

    public static function bootOptimizesPostMedia(): void
    {
        static::saving(function ($model): void {
            $model->originalCoverImage = $model->getOriginal('cover_image_path');
            $model->originalGalleryImages = collect($model->getOriginal('gallery_images') ?? [])
                ->filter()
                ->values()
                ->all();

            $model->cover_image_path = $model->optimizePostImage($model->cover_image_path, 'posts/covers');
            $model->gallery_images = $model->optimizePostGallery($model->gallery_images ?? [], 'posts/gallery');
        });

        static::saved(function ($model): void {
            $model->cleanupObsoleteMedia();
        });

        static::deleted(function ($model): void {
            $disk = Storage::disk('public');

            if (filled($model->cover_image_path)) {
                $disk->delete($model->cover_image_path);
            }

            collect($model->gallery_images ?? [])
                ->filter()
                ->each(fn (string $path) => $disk->delete($path));
        });
    }

    protected function optimizePostGallery(array $paths, string $defaultDirectory): array
    {
        return collect($paths)
            ->filter(fn ($path) => is_string($path) && $path !== '')
            ->map(fn (string $path) => $this->optimizePostImage($path, $defaultDirectory))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function optimizePostImage(?string $path, string $fallbackDirectory): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

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

        $directory = $this->resolveTargetDirectory($path, $fallbackDirectory);
        $newPath = trim($directory, '/') . '/' . Str::uuid()->toString() . '.webp';

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

    protected function resolveTargetDirectory(string $originalPath, string $fallbackDirectory): string
    {
        $directory = Str::beforeLast($originalPath, '/');

        if ($directory === $originalPath || $directory === '') {
            return trim($fallbackDirectory, '/');
        }

        return trim($directory, '/');
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

    protected function cleanupObsoleteMedia(): void
    {
        $disk = Storage::disk('public');

        if (filled($this->originalCoverImage) && $this->originalCoverImage !== $this->cover_image_path) {
            $disk->delete($this->originalCoverImage);
        }

        $current = collect($this->gallery_images ?? []);

        collect($this->originalGalleryImages)
            ->diff($current)
            ->each(fn (string $path) => $disk->delete($path));
    }
}
