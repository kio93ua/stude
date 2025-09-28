<?php

namespace App\Filament\Admin\Pages;

use App\Settings\HomePageSettings;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\FileUpload;
use Filament\Pages\SettingsPage;

class HomepageSettingsPage extends SettingsPage
{
    protected static ?string $navigationIcon  = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationLabel = 'Головна (Hero)';
    protected static ?string $navigationGroup = 'Контент сайту';
    protected static ?string $title           = 'Hero контент';

    // Прив’язка до Spatie Settings
    protected static string $settings = HomePageSettings::class;

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Головний екран (Hero)')
                ->columns(2)
                ->schema([
                    TextInput::make('hero_badge')
                        ->label('Бейдж')
                        ->required()
                        ->default('Індивідуальні заняття з англійської'),

                    TextInput::make('hero_title')
                        ->label('Заголовок')
                        ->required()
                        ->columnSpanFull(),

                    Textarea::make('hero_subtitle')
                        ->label('Підзаголовок')
                        ->rows(3)
                        ->required()
                        ->columnSpanFull(),

                    // ✅ Масив рядків без зайвих вкладених елементів
                    TagsInput::make('hero_bullets')
    ->label('Список переваг')
    ->placeholder('Додай пункт і натисни Enter')
    ->suggestions([
        'Онлайн та офлайн заняття у зручному графіку',
        'Персональний план під ваш рівень та цілі',
        'Цифрові матеріали, Д/З та регулярний фідбек',
    ])
    ->separator(',')          // дозволяє вводити через кому
    ->reorderable()
    ->afterStateHydrated(function ($set, $state) {
        // якщо збережено рядком — перетвори на масив
        if (is_string($state)) {
            $set('hero_bullets', preg_split('/\s*,\s*/', $state, -1, PREG_SPLIT_NO_EMPTY));
        } elseif (! is_array($state)) {
            $set('hero_bullets', []);
        }
    })
    ->dehydrateStateUsing(function ($state) {
        // на виході завжди масив рядків без порожніх значень
        $arr = is_array($state)
            ? $state
            : (is_string($state) ? preg_split('/\s*,\s*/', $state, -1, PREG_SPLIT_NO_EMPTY) : []);

        $arr = array_map(fn ($v) => is_string($v) ? trim($v) : '', $arr);
        return array_values(array_filter($arr, fn ($v) => $v !== ''));
    })
    ->columnSpanFull(),

                    Forms\Components\Fieldset::make('Кнопки')
                        ->columns(2)
                        ->schema([
                            TextInput::make('hero_primary_text')->label('Primary: текст')->default('Запис на пробний урок'),
                            TextInput::make('hero_primary_href')->label('Primary: href')->default('#contact'),
                            TextInput::make('hero_secondary_text')->label('Secondary: текст')->default('Дивитися програми'),
                            TextInput::make('hero_secondary_href')->label('Secondary: href')->default('#services'),
                        ]),

                    // ✅ Завантаження зображення у public/storage/homepage
                    FileUpload::make('hero_image_path')
                        ->label('Зображення (опційно)')
                        ->image()
                        ->directory('homepage') // файли підуть у storage/app/public/homepage
                        ->disk('public')        // посилання через /storage/...
                        ->visibility('public')
                        ->imageEditor()         // простий crop/rotate
                        ->imagePreviewHeight('200')
                        ->downloadable()
                        ->openable()
                        ->columnSpanFull(),
                ]),
        ];
    }

    /**
     * Трохи страхуємо стан форми.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $defaults = [
            'hero_badge'           => '',
            'hero_title'           => '',
            'hero_subtitle'        => '',
            'hero_bullets'         => [],
            'hero_primary_text'    => null,
            'hero_primary_href'    => null,
            'hero_secondary_text'  => null,
            'hero_secondary_href'  => null,
            'hero_image_path'      => null,
        ];

        // TagsInput вже повертає масив рядків — лише прибираємо порожнє
        if (! isset($data['hero_bullets']) || ! is_array($data['hero_bullets'])) {
            $data['hero_bullets'] = [];
        } else {
            $data['hero_bullets'] = array_values(
                array_filter(array_map(fn ($v) => is_string($v) ? trim($v) : '', $data['hero_bullets']), fn ($v) => $v !== '')
            );
        }

        return array_replace($defaults, $data);
    }
}
