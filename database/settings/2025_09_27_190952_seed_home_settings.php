<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        // текстові
        $this->migrator->add('home.hero_badge', 'Індивідуальні заняття з англійської');
        $this->migrator->add('home.hero_title', 'Допоможу заговорити англійською впевнено вже за 3 місяці');
        $this->migrator->add('home.hero_subtitle', 'Я — репетитор з 8-річним досвідом підготовки до IELTS, розмовної практики та бізнес-англійської. Працюю з підлітками та дорослими, комбіную сучасні матеріали та живе спілкування.');

        // масив булетів
        $this->migrator->add('home.hero_bullets', [
            'Онлайн та офлайн заняття у зручному графіку',
            'Персональний план під ваш рівень та цілі',
            'Цифрові матеріали, Д/З та регулярний фідбек',
        ]);

        // кнопки
        $this->migrator->add('home.hero_primary_text', 'Запис на пробний урок');
        $this->migrator->add('home.hero_primary_href', '#contact');
        $this->migrator->add('home.hero_secondary_text', 'Дивитися програми');
        $this->migrator->add('home.hero_secondary_href', '#services');

        // зображення (опційно)
        $this->migrator->add('home.hero_image_path', null);
    }
};
