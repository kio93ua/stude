<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('home.lessons_badge', 'Програми навчання');
        $this->migrator->add('home.lessons_title', 'Наші уроки');
        $this->migrator->add('home.lessons_subtitle', 'Баланс розмовної практики, граматики та лексики. Кожне заняття — ще один крок до вільної англійської.');
        $this->migrator->add('home.lessons_autoplay_on_view', false);
        $this->migrator->add('home.lessons_videos', [
            ['id' => 'dQw4w9WgXcQ', 'title' => 'Intro: як ми вчимося', 'description' => 'Короткий огляд підходу та структури уроків.'],
            ['id' => 'ysz5S6PUM-U', 'title' => 'Розмовна практика: small talk', 'description' => 'Фрази для щоденного спілкування.'],
            ['id' => 'ScMzIvxBSi4', 'title' => 'Граматика без болю: часи', 'description' => 'Як швидко згадати й застосувати часи.'],
            ['id' => 'kXYiU_JCYtU', 'title' => 'Фразові дієслова', 'description' => 'Корисні конструкції для реальних ситуацій.'],
        ]);
    }

    public function down(): void
    {
        $this->migrator->delete('home.lessons_badge');
        $this->migrator->delete('home.lessons_title');
        $this->migrator->delete('home.lessons_subtitle');
        $this->migrator->delete('home.lessons_autoplay_on_view');
        $this->migrator->delete('home.lessons_videos');
    }
};
