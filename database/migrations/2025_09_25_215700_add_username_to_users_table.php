<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->after('name');
        });

        DB::table('users')
            ->select(['id', 'name', 'email', 'username'])
            ->orderBy('id')
            ->chunk(100, function ($users): void {
                foreach ($users as $user) {
                    if (! empty($user->username)) {
                        continue;
                    }

                    $base = $user->email
                        ? Str::slug(strtok($user->email, '@'))
                        : Str::slug($user->name ?: 'user');
                    $base = $base ?: 'user';

                    $candidate = $base;
                    $suffix = 1;

                    while (DB::table('users')
                        ->where('username', $candidate)
                        ->where('id', '!=', $user->id)
                        ->exists()) {
                        $suffix++;
                        $candidate = $base . '-' . $suffix;
                    }

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['username' => $candidate]);
                }
            });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('username');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_username_unique');
            $table->dropColumn('username');
        });
    }
};
