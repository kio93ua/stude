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

    public string $founder_badge = 'Історія засновника';
    public string $founder_name = 'Олена Коваль';
    public string $founder_role = 'Засновниця школи англійської';
    public ?string $founder_intro = 'Від першого учня до власної школи: шлях, помилки і відкриття, які сформували наш підхід до навчання.';
    public ?string $founder_photo_path = 'images/founder.png';
    public string $founder_photo_alt = 'Портрет засновниці';
    public ?string $founder_linkedin = 'https://www.linkedin.com/';
    public ?string $founder_instagram = 'https://www.instagram.com/';
    public ?string $founder_site = 'https://example.com';
    public array $founder_sections = [
        [
            'heading' => 'Початок шляху',
            'body' => [
                'Усе почалося з індивідуальних занять удома: один стіл, ноутбук та велике бажання допомогти студентам заговорити впевнено.',
                'Поступово сформувалася методика, що поєднує комунікативний підхід та завдання з реальних ситуацій.',
            ],
        ],
        [
            'heading' => 'Розвиток і помилки',
            'body' => [
                'Зростання — це експерименти. Частину форматів ми відкинули, залишивши тільки ті, що реально працюють.',
                'Фокус — на практиці й результаті: тестові розмови, мікроцілі та підсумки після кожного модуля.',
            ],
            'quote_text' => 'Мова — інструмент. Коли ним користуєшся щодня, він залишається гострим.',
            'quote_author' => 'Олена',
        ],
        [
            'heading' => 'Сьогодні',
            'body' => [
                'Ми зібрали найкращий досвід у структуровані програми та продовжуємо оновлювати матеріали щосеместру.',
                'Мета — дати інструменти і впевненість, щоб англійська працювала у реальному житті.',
            ],
        ],
    ];
    public array $founder_extra_sections = [
        [
            'heading' => 'Як з’явилася методика',
            'body' => [
                'Ми пробували різні формати — від класичних підручників до ситуативних рольових ігор. Залишили те, що реально працює для дорослих і підлітків: короткі міні-цілі, регулярні спікінг-сесії, фокус на лексичних блоках і практиці зі сценаріями з життя.',
                'Кожен модуль завершується підсумковими розмовами і мікро-рефлексією: що вже виходить, де є бар’єр, і як його зняти. Так все поступово перетворюється на стабільну звичку говорити.',
            ],
        ],
        [
            'heading' => 'Сьогодні та далі',
            'body' => [
                'Системно оновлюємо програми кожен семестр: додаємо сучасні теми, короткі відео, завдання на слух і говоріння та адаптуємо матеріали під реальні задачі студентів — робота, подорожі, навчання, переїзд.',
                'Наша мета — зробити англійську корисним інструментом на щодень. Ви отримуєте зрозумілий план, підтримку викладача і прозорі критерії прогресу без зайвої “води”.',
            ],
        ],
    ];

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

    public string $reviews_badge = 'Наші відгуки';
    public string $reviews_title = 'Нам довіряють — результати студентів це наша перевага';
    public string $reviews_button_text = 'Більше відгуків в Instagram';
    public string $reviews_button_url = 'https://instagram.com/your.profile';
    public array $reviews_items = [
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
// --- Teacher Vacancy (керується з адмінки) ---
public string  $vacancy_badge    = 'Вакансія';
public string  $vacancy_title    = 'Запрошуємо вчителів англійської';
public ?string $vacancy_subtitle = 'Гнучкий графік, сучасні матеріали та дружня команда. Розвивайся разом із нами й впливай на результати студентів.';
public array   $vacancy_bullets  = [
    'Гнучкий графік: обирайте слоти під себе',
    'Матеріали, планування та тім-саппорт (перевірка, клуби)',
    'Прозора оплата та бонуси за результати студентів',
];

// Один із варіантів для зображення: локальний файл у public/storage/...
public ?string $vacancy_media_path = null;
// або прямий зовнішній URL (якщо path не заданий)
public ?string $vacancy_media_url  = null;

// Кнопка
public string  $vacancy_cta_text = 'Заповнити анкету';
public string  $vacancy_cta_url  = 'https://docs.google.com/forms/d/e/1FAIpQLSew8oe-A0p3wS7omGT_u3h9ts04egW_Mr0SfIgYzXn8tQUekA/viewform?usp=header';
    public static function group(): string
    {
        return 'home';
    }
}
