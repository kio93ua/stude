<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class HomePageSettings extends Settings
{
    public string  $hero_badge = 'Індивідуальні заняття з англійської';
    public string  $hero_title = 'Допоможу заговорити англійською впевнено вже за 3 місяці';
    public string  $hero_subtitle = 'Я — репетитор з 8-річним досвідом...';
    public array   $hero_bullets = [
        'Онлайн та офлайн заняття у зручному графіку',
        'Персональний план під ваш рівень та цілі',
        'Цифрові матеріали, Д/З та регулярний фідбек',
    ];
    public ?string $hero_primary_text = 'Запис на пробний урок';
    public ?string $hero_primary_href = '#contact';
    public ?string $hero_secondary_text = 'Дивитися програми';
    public ?string $hero_secondary_href = '#services';

    // шлях у public/storage/homepage/....
    public ?string $hero_image_path = null;

    public string $pricing_badge = 'Пакети занять';
    public string $pricing_title = 'Обери формат, що пасує саме тобі';
    public string $pricing_subtitle = 'Три прозорі варіанти з чіткими перевагами.';
    public string $pricing_currency = '₴';

    public array $pricing_plans = [
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
    ];

    public string $advantages_badge = 'Переваги навчання';
    public string $advantages_title = 'Вчися ефективно — у зручному для тебе форматі';
    public ?string $advantages_subtitle = null;
    public string $advantages_cta_text = 'Записатися';
    public string $advantages_cta_href = '#contact';

    public array $advantages_items = [
        [
            'title' => 'Ігрові методи',
            'desc' => 'Інтерактиви, рольові сценарії та міні-ігри — мотивація росте, страх помилок зникає.',
            'bullets' => ['Щотижневі челенджі', 'Сценарії з реального життя', 'Веселі практики замість нудної теорії'],
            'image_path' => 'images/adv/gamified.jpg',
            'image_alt' => 'Ігрові методи',
            'icons' => [],
        ],
        [
            'title' => 'Сучасна програма вивчення',
            'desc' => 'Комунікативний підхід, мікрозвички та трек прогресу — чіткий результат щотижня.',
            'bullets' => ['Модульна структура', 'Практика > теорія', 'Персональні рекомендації'],
            'image_path' => 'images/adv/modern.jpg',
            'image_alt' => 'Сучасна програма',
            'icons' => [],
        ],
        [
            'title' => 'Задоволені учні',
            'desc' => 'Тепла дружня атмосфера й підтримка — легше говорити впевнено.',
            'bullets' => ['Малі групи або 1-на-1', 'Зворотний зв’язок щотижня', 'Клуби розмовної практики'],
            'image_path' => 'images/adv/happy.jpg',
            'image_alt' => 'Задоволені учні',
            'icons' => [],
        ],
    ];

    public string $lessons_badge = 'Програми навчання';
    public string $lessons_title = 'Наші уроки';
    public string $lessons_subtitle = 'Баланс розмовної практики, граматики та лексики. Кожне заняття — ще один крок до вільної англійської.';
    public bool $lessons_autoplay_on_view = false;
    public array $lessons_videos = [
        ['id' => 'dQw4w9WgXcQ', 'title' => 'Intro: як ми вчимося', 'description' => 'Короткий огляд підходу та структури уроків.'],
        ['id' => 'ysz5S6PUM-U', 'title' => 'Розмовна практика: small talk', 'description' => 'Фрази для щоденного спілкування.'],
        ['id' => 'ScMzIvxBSi4', 'title' => 'Граматика без болю: часи', 'description' => 'Як швидко згадати й застосувати часи.'],
        ['id' => 'kXYiU_JCYtU', 'title' => 'Фразові дієслова', 'description' => 'Корисні конструкції для реальних ситуацій.'],
    ];

    public string $faq_title = 'FAQ (коротко)';
    public ?string $faq_subtitle = '';

    public array $faq_items = [
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
    ];

    public static function group(): string
    {
        return 'home';
    }
}
