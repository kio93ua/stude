<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration {
    public function up(): void
    {
        $this->migrator->inGroup('home', function () {
            $this->migrator->add('home.vacancy_badge', 'Вакансія');
            $this->migrator->add('home.vacancy_title', 'Запрошуємо вчителів англійської');
            $this->migrator->add('home.vacancy_subtitle', 'Гнучкий графік, сучасні матеріали та дружня команда. Розвивайся разом із нами й впливай на результати студентів.');
            $this->migrator->add('home.vacancy_bullets', [
                'Гнучкий графік: обирайте слоти під себе',
                'Матеріали, планування та тім-саппорт (перевірка, клуби)',
                'Прозора оплата та бонуси за результати студентів',
            ]);
            $this->migrator->add('home.vacancy_media_path', null);
            $this->migrator->add('home.vacancy_media_url',  null);
            $this->migrator->add('home.vacancy_cta_text', 'Заповнити анкету');
            $this->migrator->add('home.vacancy_cta_url',  'https://docs.google.com/forms/d/e/1FAIpQLSew8oe-A0p3wS7omGT_u3h9ts04egW_Mr0SfIgYzXn8tQUekA/viewform?usp=header');
        });
    }
};
