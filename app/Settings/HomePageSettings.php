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

    public static function group(): string
    {
        return 'home';
    }
}
