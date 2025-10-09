<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('home.reviews_badge', 'Наші відгуки');
        $this->migrator->add('home.reviews_title', 'Нам довіряють — результати студентів це наша перевага');
        $this->migrator->add('home.reviews_button_text', 'Більше відгуків в Instagram');
        $this->migrator->add('home.reviews_button_url', 'https://instagram.com/your.profile');
        $this->migrator->add('home.reviews_items', [
            [
                'name' => 'Марія Коваль',
                'avatar_path' => null,
                'avatar_url' => 'https://i.pravatar.cc/96?img=1',
                'stars' => 5,
                'course' => 'IELTS',
                'text' => 'Класні уроки, багато розмовної практики і чіткий план підготовки. За місяць стала впевненіше говорити, рекомендую!',
            ],
            [
                'name' => 'Олег С.',
                'avatar_path' => null,
                'avatar_url' => 'https://i.pravatar.cc/96?img=2',
                'stars' => 5,
                'course' => 'Business English',
                'text' => 'Сучасні завдання, реальні кейси з роботи. Дуже подобається формат — завжди тримає фокус і дає результат.',
            ],
            [
                'name' => 'Ірина Ч.',
                'avatar_path' => null,
                'avatar_url' => 'https://i.pravatar.cc/96?img=3',
                'stars' => 5,
                'course' => 'General',
                'text' => 'Дуже комфортно й ефективно. Індивідуальний підхід, помітний прогрес вже за кілька тижнів.',
            ],
            [
                'name' => 'Андрій П.',
                'avatar_path' => null,
                'avatar_url' => 'https://i.pravatar.cc/96?img=4',
                'stars' => 5,
                'course' => 'Speaking Club',
                'text' => 'Динамічні зустрічі, багато говоріння, виправлення помилок у реальному часі — супер!',
            ],
        ]);
    }

    public function down(): void
    {
        $this->migrator->delete('home.reviews_badge');
        $this->migrator->delete('home.reviews_title');
        $this->migrator->delete('home.reviews_button_text');
        $this->migrator->delete('home.reviews_button_url');
        $this->migrator->delete('home.reviews_items');
    }
};
