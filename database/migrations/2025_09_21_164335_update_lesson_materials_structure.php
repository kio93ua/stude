<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lesson_materials', function (Blueprint $table) {
            if (! Schema::hasColumn('lesson_materials', 'resource_links')) {
                $table->json('resource_links')->nullable()->after('external_url');
            }

            if (! Schema::hasColumn('lesson_materials', 'attachments')) {
                $table->json('attachments')->nullable()->after('resource_links');
            }

            if (! Schema::hasColumn('lesson_materials', 'images')) {
                $table->json('images')->nullable()->after('attachments');
            }
        });

        if (Schema::hasColumn('lesson_materials', 'type')) {
            DB::statement("ALTER TABLE lesson_materials MODIFY type VARCHAR(191) NULL DEFAULT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lesson_materials', function (Blueprint $table) {
            $columns = collect(['resource_links', 'attachments', 'images'])
                ->filter(fn ($column) => Schema::hasColumn('lesson_materials', $column))
                ->all();

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });

        if (Schema::hasColumn('lesson_materials', 'type')) {
            DB::statement("ALTER TABLE lesson_materials MODIFY type VARCHAR(191) NOT NULL DEFAULT 'link'");
        }
    }
};
