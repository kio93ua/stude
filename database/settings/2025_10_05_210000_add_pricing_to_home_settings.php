<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('home.pricing_badge', 'Пакети занять');
        $this->migrator->add('home.pricing_title', 'Обери формат, що пасує саме тобі');
        $this->migrator->add('home.pricing_subtitle', 'Три прозорі варіанти з чіткими перевагами.');
        $this->migrator->add('home.pricing_currency', '₴');

        $this->migrator->add('home.pricing_plans', [
            'group' => [
                'title' => 'Групові',
                'label' => 'mini-group',
                'description' => 'Більше практики у малій групі — вигідно та драйвово.',
                'price' => '250',
                'meta' => '3 уроки / тиждень · 60 хв',
                'features' => [
                    'Міні-групи до 6 осіб',
                    'Ігрові активності',
                    'Speaking-клуб 2×/міс',
                ],
                'cta_text' => 'Записатися',
                'cta_href' => '#contact',
            ],
            'pair' => [
                'title' => 'Пари',
                'label' => 'duo',
                'description' => 'Вчимося разом — баланс вартості та прогресу.',
                'price' => '400',
                'meta' => '2 уроки / тиждень · 60 хв',
                'features' => [
                    '2× / тиждень · 60 хв',
                    'Speaking-клуб 1×/міс',
                    'Зворотний зв’язок',
                ],
                'cta_text' => 'Обрати пару',
                'cta_href' => '#contact',
            ],
            'individual' => [
                'title' => 'Індивідуальні',
                'label' => '1-on-1',
                'description' => 'Персональний темп, фокус на ваших цілях та сильна підтримка.',
                'price' => '600',
                'meta' => '1 урок / тиждень · 50 хв',
                'features' => [
                    'Персональний план',
                    'Домашні з перевіркою',
                    'Гнучкий графік',
                ],
                'cta_text' => 'Записатися',
                'cta_href' => '#contact',
            ],
        ]);
    }

    public function down(): void
    {
        $this->migrator->delete('home.pricing_badge');
        $this->migrator->delete('home.pricing_title');
        $this->migrator->delete('home.pricing_subtitle');
        $this->migrator->delete('home.pricing_currency');
        $this->migrator->delete('home.pricing_plans');
    }
};
