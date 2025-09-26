<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('username');
            $table->string('last_name')->nullable()->after('first_name');
        });

        DB::table('users')->select(['id', 'name'])->orderBy('id')->chunk(100, function ($users): void {
            foreach ($users as $user) {
                if (empty($user->name)) {
                    continue;
                }

                $parts = preg_split('/\s+/', trim($user->name), 2);
                $first = $parts[0] ?? null;
                $last = $parts[1] ?? null;

                DB::table('users')->where('id', $user->id)->update([
                    'first_name' => $first,
                    'last_name' => $last,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};
