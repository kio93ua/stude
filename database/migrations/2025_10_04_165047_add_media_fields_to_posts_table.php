<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (! Schema::hasColumn('posts', 'cover_image_path')) {
                $table->string('cover_image_path')->nullable()->after('body');
            }

            if (! Schema::hasColumn('posts', 'gallery_images')) {
                $table->json('gallery_images')->nullable()->after('cover_image_path');
            }

            if (! Schema::hasColumn('posts', 'youtube_url')) {
                $table->string('youtube_url')->nullable()->after('gallery_images');
            }

            if (! Schema::hasColumn('posts', 'is_published')) {
                $table->boolean('is_published')->default(false)->after('youtube_url');
            }

            if (! Schema::hasColumn('posts', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('is_published');
            }
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            if (Schema::hasColumn('posts', 'published_at')) {
                $table->dropColumn('published_at');
            }

            if (Schema::hasColumn('posts', 'is_published')) {
                $table->dropColumn('is_published');
            }

            if (Schema::hasColumn('posts', 'youtube_url')) {
                $table->dropColumn('youtube_url');
            }

            if (Schema::hasColumn('posts', 'gallery_images')) {
                $table->dropColumn('gallery_images');
            }

            if (Schema::hasColumn('posts', 'cover_image_path')) {
                $table->dropColumn('cover_image_path');
            }
        });
    }
};
