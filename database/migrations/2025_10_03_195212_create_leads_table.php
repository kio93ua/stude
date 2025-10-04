<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('leads', function (Blueprint $table) {
    $table->id();
    $table->string('full_name');
    $table->unsignedTinyInteger('age')->nullable();
    $table->string('level', 10);
    $table->string('phone', 50);
    $table->string('email');
    $table->enum('intent', ['consultation','first_lesson','info']);
    $table->text('questions')->nullable();
    $table->json('meta')->nullable(); // ip, user-agent тощо
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
