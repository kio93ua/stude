<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('home.founder_badge', 'Історія засновника');
        $this->migrator->add('home.founder_name', 'Олена Коваль');
        $this->migrator->add('home.founder_role', 'Засновниця школи англійської');
        $this->migrator->add('home.founder_intro', 'Від першого учня до власної школи: шлях, помилки і відкриття, які сформували наш підхід до навчання.');
        $this->migrator->add('home.founder_photo_path', 'images/founder.png');
        $this->migrator->add('home.founder_photo_alt', 'Портрет засновниці');
        $this->migrator->add('home.founder_linkedin', 'https://www.linkedin.com/');
        $this->migrator->add('home.founder_instagram', 'https://www.instagram.com/');
        $this->migrator->add('home.founder_site', 'https://example.com');
        $this->migrator->add('home.founder_sections', [
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
        ]);
        $this->migrator->add('home.founder_extra_sections', [
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
        ]);
    }

    public function down(): void
    {
        $this->migrator->delete('home.founder_badge');
        $this->migrator->delete('home.founder_name');
        $this->migrator->delete('home.founder_role');
        $this->migrator->delete('home.founder_intro');
        $this->migrator->delete('home.founder_photo_path');
        $this->migrator->delete('home.founder_photo_alt');
        $this->migrator->delete('home.founder_linkedin');
        $this->migrator->delete('home.founder_instagram');
        $this->migrator->delete('home.founder_site');
        $this->migrator->delete('home.founder_sections');
        $this->migrator->delete('home.founder_extra_sections');
    }
};
