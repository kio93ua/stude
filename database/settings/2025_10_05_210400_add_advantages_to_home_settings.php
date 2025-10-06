<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('home.advantages_badge', 'Переваги навчання');
        $this->migrator->add('home.advantages_title', 'Вчися ефективно — у зручному для тебе форматі');
        $this->migrator->add('home.advantages_subtitle', null);
        $this->migrator->add('home.advantages_cta_text', 'Записатися');
        $this->migrator->add('home.advantages_cta_href', '#contact');
        $this->migrator->add('home.advantages_items', [
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
        ]);
    }

    public function down(): void
    {
        $this->migrator->delete('home.advantages_badge');
        $this->migrator->delete('home.advantages_title');
        $this->migrator->delete('home.advantages_subtitle');
        $this->migrator->delete('home.advantages_cta_text');
        $this->migrator->delete('home.advantages_cta_href');
        $this->migrator->delete('home.advantages_items');
    }
};
