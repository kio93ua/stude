<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('home.faq_title', 'FAQ (коротко)');
        $this->migrator->add('home.faq_subtitle', '');
        $this->migrator->add('home.faq_items', [
            [
                'q' => 'Скільки часу потрібно, щоб заговорити?',
                'a' => '8–12 тижнів за планом: щоденні міні-сесії + 1–2 розмовні уроки на тиждень.',
            ],
            [
                'q' => 'Граматика обовʼязкова?',
                'a' => 'Лише ~20% від заняття. Подання — через приклади й одразу в говорінні.',
            ],
            [
                'q' => 'Онлайн чи офлайн?',
                'a' => 'Формат змішаний: обираєте за графіком. Є запис пропущених занять.',
            ],
            [
                'q' => 'З яких рівнів навчаємо?',
                'a' => 'Від Starter/A1 до Upper-Intermediate/B2. Є підготовчі групи для початку з нуля.',
            ],
            [
                'q' => 'Чи є домашні?',
                'a' => 'Так, короткі практики 10–15 хв. щодня + трек мотивації.',
            ],
            [
                'q' => 'Матеріали включені?',
                'a' => 'Так. Доступ до власних конспектів, карток і відео в LMS.',
            ],
        ]);
    }

    public function down(): void
    {
        $this->migrator->delete('home.faq_title');
        $this->migrator->delete('home.faq_subtitle');
        $this->migrator->delete('home.faq_items');
    }
};
