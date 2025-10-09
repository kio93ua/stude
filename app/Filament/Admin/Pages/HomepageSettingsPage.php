<?php

namespace App\Filament\Admin\Pages;

use App\Settings\HomePageSettings;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HomepageSettingsPage extends SettingsPage
{
    protected static ?string $navigationIcon  = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationLabel = 'Головна (Hero)';
    protected static ?string $navigationGroup = 'Контент сайту';
    protected static ?string $title           = 'Hero контент';

    // Прив’язка до Spatie Settings
    protected static string $settings = HomePageSettings::class;

    /** Поточний (старий) шлях до hero-зображення до сабміту форми */
    protected ?string $oldHeroImagePath = null;
    protected ?string $oldFounderPhotoPath = null;
    protected array $oldAdvantageImages = [];
    protected array $oldAdvantageIcons = [];
    protected array $oldReviewAvatars = [];
    protected ?string $oldVacancyMediaPath = null;


    public function mount(): void
    {
        parent::mount();
        // зчитуємо поточне значення перед редагуванням, щоб мати що видалити після апдейту
        $settings = app(HomePageSettings::class);
        $this->oldHeroImagePath = $settings->hero_image_path ?: null;
        $photoPath = $settings->founder_photo_path ?: null;
        $this->oldFounderPhotoPath = is_string($photoPath) && ! Str::startsWith($photoPath, ['http://', 'https://'])
            ? $photoPath
            : null;
        $this->oldAdvantageImages = collect($settings->advantages_items ?? [])->pluck('image_path')->filter()->values()->all();
        $this->oldAdvantageIcons = collect($settings->advantages_items ?? [])->pluck('icons')->flatten()->filter()->values()->all();
        $this->oldReviewAvatars = collect($settings->reviews_items ?? [])
            ->pluck('avatar_path')
            ->filter(fn ($path) => is_string($path) && $path !== '' && ! Str::startsWith($path, ['http://', 'https://', '/']))
            ->values()
            ->all();
            $this->oldVacancyMediaPath = app(\App\Settings\HomePageSettings::class)->vacancy_media_path ?: null;

    }

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
                        // додаткові обмеження — не ламають існуючу логіку
                        ->maxSize(8192) // до 8 МБ
                        ->acceptedFileTypes(['image/jpeg','image/png','image/webp','image/gif'])
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Блок тарифів (Pricing)')
                ->columns(2)
                ->schema([
                    TextInput::make('pricing_badge')
                        ->label('Бейдж над блоком')
                        ->required()
                        ->maxLength(60),

                    TextInput::make('pricing_currency')
                        ->label('Символ валюти')
                        ->required()
                        ->maxLength(4),

                    TextInput::make('pricing_title')
                        ->label('Заголовок блоку')
                        ->required()
                        ->columnSpanFull(),

                    Textarea::make('pricing_subtitle')
                        ->label('Короткий опис / підзаголовок')
                        ->rows(2)
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\Fieldset::make('Картка: Групові')
                        ->schema($this->pricingCardSchema('pricing_plans.group'))
                        ->columns(2)
                        ->columnSpanFull(),

                    Forms\Components\Fieldset::make('Картка: Пари')
                        ->schema($this->pricingCardSchema('pricing_plans.pair'))
                        ->columns(2)
                        ->columnSpanFull(),

                    Forms\Components\Fieldset::make('Картка: Індивідуальні')
                        ->schema($this->pricingCardSchema('pricing_plans.individual'))
                        ->columns(2)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Історія засновника')
                ->columns(2)
                ->schema([
                    TextInput::make('founder_badge')
                        ->label('Бейдж')
                        ->required()
                        ->maxLength(80),

                    TextInput::make('founder_name')
                        ->label('Ім’я')
                        ->required(),

                    TextInput::make('founder_role')
                        ->label('Роль / титул')
                        ->required(),

                    Textarea::make('founder_intro')
                        ->label('Підзаголовок під ім’ям')
                        ->rows(3)
                        ->columnSpanFull(),

                    FileUpload::make('founder_photo_path')
                        ->label('Фото засновника')
                        ->image()
                        ->directory('founder')
                        ->disk('public')
                        ->visibility('public')
                        ->imageEditor()
                        ->imagePreviewHeight('220')
                        ->maxSize(8192)
                        ->acceptedFileTypes(['image/jpeg','image/png','image/webp','image/gif'])
                        ->columnSpanFull(),

                    TextInput::make('founder_photo_alt')
                        ->label('ALT для фото')
                        ->maxLength(120)
                        ->columnSpanFull(),

                    TextInput::make('founder_linkedin')
                        ->label('LinkedIn')
                        ->nullable(),

                    TextInput::make('founder_instagram')
                        ->label('Instagram')
                        ->nullable(),

                    TextInput::make('founder_site')
                        ->label('Персональний сайт')
                        ->nullable(),

                    Repeater::make('founder_sections')
                        ->label('Основні секції історії')
                        ->schema([
                            TextInput::make('heading')
                                ->label('Заголовок')
                                ->required(),

                            Repeater::make('body')
                                ->label('Абзаци')
                                ->schema([
                                    Textarea::make('value')
                                        ->label('Текст')
                                        ->rows(2)
                                        ->required(),
                                ])
                                ->minItems(1)
                                ->defaultItems(1) 
                                ->addActionLabel('Додати абзац')
                                ->columnSpanFull()
                                ->dehydrateStateUsing(fn ($state) => $this->normalizeParagraphRepeater($state)),

                            Textarea::make('quote_text')
                                ->label('Цитата (опційно)')
                                ->rows(2)
                                ->columnSpanFull(),

                            TextInput::make('quote_author')
                                ->label('Автор цитати')
                                ->maxLength(120),
                        ])
                        ->minItems(1)
                        ->addActionLabel('Додати секцію')
                        ->reorderable()
                        ->columnSpanFull(),

                    Repeater::make('founder_extra_sections')
                        ->label('Додаткові розділи')
                        ->schema([
                            TextInput::make('heading')
                                ->label('Заголовок')
                                ->required(),

                            Repeater::make('body')
                                ->label('Абзаци')
                                ->schema([
                                    Textarea::make('value')
                                        ->label('Текст')
                                        ->rows(2)
                                        ->required(),
                                ])
                                ->minItems(0)           
                                 ->defaultItems(0) 
                                ->columnSpanFull()
                                ->dehydrateStateUsing(fn ($state) => $this->normalizeParagraphRepeater($state)),
                        ])
                        ->addActionLabel('Додати розділ')
                        ->reorderable()
                        ->minItems(0)
                        ->maxItems(5)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Переваги (Advantages)')
                ->columns(2)
                ->schema([
                    TextInput::make('advantages_badge')
                        ->label('Бейдж над блоком')
                        ->required()
                        ->maxLength(80),

                    TextInput::make('advantages_title')
                        ->label('Заголовок блоку')
                        ->required()
                        ->columnSpanFull(),

                    Textarea::make('advantages_subtitle')
                        ->label('Підзаголовок')
                        ->rows(2)
                        ->columnSpanFull(),

                    TextInput::make('advantages_cta_text')
                        ->label('Текст кнопки')
                        ->required(),

                    TextInput::make('advantages_cta_href')
                        ->label('Посилання кнопки')
                        ->required()
                        ->maxLength(255)
                        ->default('#contact'),

                    Repeater::make('advantages_items')
                        ->label('Слайди переваг')
                        ->schema([
                            TextInput::make('title')
                                ->label('Заголовок')
                                ->required(),

                            Textarea::make('desc')
                                ->label('Опис (опційно)')
                                ->rows(2)
                                ->columnSpanFull(),

                            TagsInput::make('bullets')
                                ->label('Пункти списку')
                                ->placeholder('Додайте пункт і натисніть Enter')
                                ->separator(',')
                                ->reorderable()
                                ->afterStateHydrated(function ($set, $state) {
                                    if (is_string($state)) {
                                        $set('bullets', preg_split('/\s*,\s*/', $state, -1, PREG_SPLIT_NO_EMPTY));
                                    } elseif (! is_array($state)) {
                                        $set('bullets', []);
                                    }
                                })
                                ->dehydrateStateUsing(fn ($state) => $this->normalizeStringArray($state))
                                ->columnSpanFull(),

                            FileUpload::make('image_path')
                                ->label('Фото')
                                ->image()
                                ->required()
                                ->directory('advantages')
                                ->disk('public')
                                ->visibility('public')
                                ->imageEditor()
                                ->imagePreviewHeight('160')
                                ->maxSize(8192)
                                ->acceptedFileTypes(['image/jpeg','image/png','image/webp','image/gif'])
                                ->columnSpanFull(),

                            TextInput::make('image_alt')
                                ->label('ALT для фото')
                                ->maxLength(120)
                                ->columnSpanFull(),

                            FileUpload::make('icons')
                                ->label('Додаткові іконки (опційно)')
                                ->image()
                                ->disk('public')
                                ->directory('advantages/icons')
                                ->visibility('public')
                                ->multiple()
                                ->reorderable()
                                ->maxFiles(6)
                                ->maxSize(2048)
                                ->imageEditor()
                                ->columnSpanFull(),
                        ])
                        ->addActionLabel('Додати перевагу')
                        ->reorderable()
                        ->minItems(3)
                        ->maxItems(8)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('FAQ')
                ->columns(2)
                ->schema([
                    TextInput::make('faq_title')
                        ->label('Заголовок блоку')
                        ->required()
                        ->columnSpanFull(),

                    Textarea::make('faq_subtitle')
                        ->label('Підзаголовок')
                        ->rows(2)
                        ->columnSpanFull(),

                    Repeater::make('faq_items')
                        ->label('Питання та відповіді')
                        ->schema([
                            Textarea::make('q')
                                ->label('Питання')
                                ->rows(2)
                                ->required()
                                ->columnSpanFull(),

                            RichEditor::make('a')
                                ->label('Відповідь')
                                ->toolbarButtons([
                                    'bold', 'italic', 'underline', 'link', 'orderedList', 'bulletList', 'blockquote'
                                ])
                                ->columnSpanFull()
                                ->required(),
                        ])
                        ->addActionLabel('Додати питання')
                        ->reorderable()
                        ->defaultItems(6)
                        ->minItems(1)
                        ->maxItems(12)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Блок уроків (Lessons)')
                ->columns(2)
                ->schema([
                    TextInput::make('lessons_badge')
                        ->label('Бейдж')
                        ->required()
                        ->maxLength(80),

                    TextInput::make('lessons_title')
                        ->label('Заголовок')
                        ->required()
                        ->columnSpanFull(),

                    Textarea::make('lessons_subtitle')
                        ->label('Підзаголовок')
                        ->rows(2)
                        ->columnSpanFull(),

                    Toggle::make('lessons_autoplay_on_view')
                        ->label('Автоматично відтворювати відео при вході у вʼюпорт')
                        ->helperText('Якщо вимкнути, відео стартує лише після кліку.'),

                    Repeater::make('lessons_videos')
                        ->label('Відео (YouTube)')
                        ->schema([
                            TextInput::make('id')
                                ->label('YouTube ID або URL')
                                ->required()
                                ->maxLength(100)
                                ->afterStateUpdated(fn ($state, callable $set) => $set('id', $this->extractYoutubeId($state))),

                            TextInput::make('title')
                                ->label('Заголовок відео')
                                ->required()
                                ->columnSpan(2),

                            Textarea::make('description')
                                ->label('Опис (опційно)')
                                ->rows(2)
                                ->columnSpan(2),
                        ])
                        ->addActionLabel('Додати відео')
                        ->reorderable()
                        ->minItems(1)
                        ->maxItems(8)
                        ->columnSpanFull(),
                ]),
             Forms\Components\Section::make('Вакансія (TeacherVacancy)')
    ->columns(2)
    ->schema([
        TextInput::make('vacancy_badge')->label('Бейдж')->required()->maxLength(60),
        TextInput::make('vacancy_title')->label('Заголовок')->required()->columnSpanFull(),
        Textarea::make('vacancy_subtitle')->label('Підзаголовок')->rows(2)->columnSpanFull(),

        TagsInput::make('vacancy_bullets')
            ->label('Пункти списку')
            ->separator(',')
            ->reorderable()
            ->afterStateHydrated(function ($set, $state) {
                if (is_string($state)) $set('vacancy_bullets', preg_split('/\s*,\s*/', $state, -1, PREG_SPLIT_NO_EMPTY));
                elseif (!is_array($state)) $set('vacancy_bullets', []);
            })
            ->dehydrateStateUsing(fn($state) => $this->normalizeStringArray($state))
            ->columnSpanFull(),

        // зображення (локальний upload)
        FileUpload::make('vacancy_media_path')
            ->label('Зображення (локально)')
            ->image()
            ->directory('vacancy')
            ->disk('public')
            ->visibility('public')
            ->imageEditor()
            ->imagePreviewHeight('200')
            ->maxSize(8192)
            ->acceptedFileTypes(['image/jpeg','image/png','image/webp','image/gif'])
            ->columnSpanFull(),

        // альтернативно, прямий URL
        TextInput::make('vacancy_media_url')
            ->label('Зображення (URL)')
            ->helperText('Якщо заповнено, має пріоритет над локальним файлом.')
            ->maxLength(255)
            ->columnSpanFull(),

        TextInput::make('vacancy_cta_text')->label('Текст кнопки')->required(),
        TextInput::make('vacancy_cta_url')->label('Посилання кнопки')->required()->maxLength(255),
    ]),

            Forms\Components\Section::make('Відгуки (Reviews)')
                ->columns(2)
                ->schema([
                    TextInput::make('reviews_badge')
                        ->label('Бейдж')
                        ->required()
                        ->maxLength(80),

                    TextInput::make('reviews_title')
                        ->label('Заголовок')
                        ->required()
                        ->columnSpanFull(),

                    TextInput::make('reviews_button_text')
                        ->label('Текст кнопки')
                        ->required()
                        ->maxLength(120),

                    TextInput::make('reviews_button_url')
                        ->label('Посилання кнопки')
                        ->required()
                        ->maxLength(255),

                    Repeater::make('reviews_items')
                        ->label('Відгуки')
                        ->schema([
                            TextInput::make('name')
                                ->label("Ім'я")
                                ->required(),

                            TextInput::make('course')
                                ->label('Курс / контекст')
                                ->maxLength(80),

                            TextInput::make('stars')
                                ->label('Зірок (0-5)')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(5)
                                ->default(5)
                                ->columnSpan(1),

                            Textarea::make('text')
                                ->label('Текст відгуку')
                                ->rows(3)
                                ->required()
                                ->columnSpanFull(),

                            FileUpload::make('avatar_path')
                                ->label('Аватар (завантажити)')
                                ->image()
                                ->directory('reviews/avatars')
                                ->disk('public')
                                ->visibility('public')
                                ->imageEditor()
                                ->imagePreviewHeight('96')
                                ->maxSize(4096)
                                ->acceptedFileTypes(['image/jpeg','image/png','image/webp','image/gif'])
                                ->columnSpan(1),

                            TextInput::make('avatar_url')
                                ->label('Аватар (URL, якщо без завантаження)')
                                ->maxLength(255)
                                ->columnSpan(1),
                        ])
                        ->addActionLabel('Додати відгук')
                        ->reorderable()
                        ->minItems(1)
                        ->maxItems(12)
                        ->columnSpanFull(),
                ]),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['founder_sections'] = $this->prepareSectionsForForm($data['founder_sections'] ?? [], true);
        $data['founder_extra_sections'] = $this->prepareSectionsForForm($data['founder_extra_sections'] ?? [], false);
        $data['reviews_items'] = $this->prepareReviewsForForm($data['reviews_items'] ?? []);

        return $data;
    }

    /**
     * Трохи страхуємо стан форми + оптимізуємо/конвертуємо hero зображення.
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
            'founder_badge'        => '',
            'founder_name'         => '',
            'founder_role'         => '',
            'founder_intro'        => null,
            'founder_photo_path'   => null,
            'founder_photo_alt'    => '',
            'founder_linkedin'     => null,
            'founder_instagram'    => null,
            'founder_site'         => null,
            'founder_sections'     => [],
            'founder_extra_sections' => [],
            'pricing_badge'        => '',
            'pricing_title'        => '',
            'pricing_subtitle'     => '',
            'pricing_currency'     => '₴',
            'pricing_plans'        => [],
            'advantages_badge'     => '',
            'advantages_title'     => '',
            'advantages_subtitle'  => '',
            'advantages_cta_text'  => '',
            'advantages_cta_href'  => '',
            'advantages_items'     => [],
            'faq_title'            => '',
            'faq_subtitle'         => '',
            'faq_items'            => [],
            'lessons_badge'        => '',
            'lessons_title'        => '',
            'lessons_subtitle'     => '',
            'lessons_autoplay_on_view' => false,
            'lessons_videos'       => [],
            'reviews_badge'        => '',
            'reviews_title'        => '',
            'reviews_button_text'  => '',
            'reviews_button_url'   => '',
            'reviews_items'        => [],
        ];

        // TagsInput вже повертає масив рядків — лише прибираємо порожнє
        if (! isset($data['hero_bullets']) || ! is_array($data['hero_bullets'])) {
            $data['hero_bullets'] = [];
        } else {
            $data['hero_bullets'] = array_values(
                array_filter(
                    array_map(fn ($v) => is_string($v) ? trim($v) : '', $data['hero_bullets']),
                    fn ($v) => $v !== ''
                )
            );
        }

        $data['pricing_badge']    = $this->sanitizeString($data['pricing_badge'] ?? '');
        $data['pricing_title']    = $this->sanitizeString($data['pricing_title'] ?? '');
        $data['pricing_subtitle'] = $this->sanitizeString($data['pricing_subtitle'] ?? '');
        $data['pricing_currency'] = $this->sanitizeCurrency($data['pricing_currency'] ?? '');

        $planDefaults = app(HomePageSettings::class)->pricing_plans;
        $inputPlans = is_array($data['pricing_plans'] ?? null) ? $data['pricing_plans'] : [];
        $normalizedPlans = [];
        foreach ($planDefaults as $key => $planDefault) {
            $incoming = is_array($inputPlans[$key] ?? null) ? $inputPlans[$key] : [];
            $normalizedPlans[$key] = [
                'title'       => $this->sanitizeString($incoming['title'] ?? $planDefault['title'] ?? ''),
                'label'       => $this->sanitizeString($incoming['label'] ?? $planDefault['label'] ?? ''),
                'description' => $this->sanitizeString($incoming['description'] ?? $planDefault['description'] ?? ''),
                'price'       => $this->sanitizePrice($incoming['price'] ?? $planDefault['price'] ?? ''),
                'meta'        => $this->sanitizeString($incoming['meta'] ?? $planDefault['meta'] ?? ''),
                'features'    => $this->normalizeStringArray($incoming['features'] ?? $planDefault['features'] ?? []),
                'cta_text'    => $this->sanitizeString($incoming['cta_text'] ?? $planDefault['cta_text'] ?? ''),
                'cta_href'    => $this->sanitizeUrl($incoming['cta_href'] ?? $planDefault['cta_href'] ?? ''),
            ];
        }
        $data['pricing_plans'] = $normalizedPlans;

        $data['founder_badge'] = $this->sanitizeString($data['founder_badge'] ?? '');
        $data['founder_name'] = $this->sanitizeString($data['founder_name'] ?? '');
        $data['founder_role'] = $this->sanitizeString($data['founder_role'] ?? '');
        $data['founder_intro'] = $this->sanitizeNullableString($data['founder_intro'] ?? null);
        $data['founder_photo_alt'] = $this->sanitizeString($data['founder_photo_alt'] ?? '');
        $data['founder_linkedin'] = $this->sanitizeNullableUrl($data['founder_linkedin'] ?? null);
        $data['founder_instagram'] = $this->sanitizeNullableUrl($data['founder_instagram'] ?? null);
        $data['founder_site'] = $this->sanitizeNullableUrl($data['founder_site'] ?? null);
        $data['founder_sections'] = $this->normalizeFounderSections($data['founder_sections'] ?? []);
        $data['founder_extra_sections'] = $this->normalizeFounderExtras($data['founder_extra_sections'] ?? []);

        if (array_key_exists('founder_photo_path', $data)) {
            $newPhotoPath = is_string($data['founder_photo_path']) ? trim($data['founder_photo_path']) : null;

            if ($newPhotoPath) {
                $optimized = $this->optimizeAndReplacePublicImage($newPhotoPath, directory: 'founder', maxBytes: 3 * 1024 * 1024);

                if ($optimized) {
                    if ($this->oldFounderPhotoPath && $this->oldFounderPhotoPath !== $optimized) {
                        Storage::disk('public')->delete($this->sanitizeRelative($this->oldFounderPhotoPath));
                    }
                    $data['founder_photo_path'] = $optimized;
                    $this->oldFounderPhotoPath = $optimized;
                }
            } else {
                if ($this->oldFounderPhotoPath) {
                    Storage::disk('public')->delete($this->sanitizeRelative($this->oldFounderPhotoPath));
                }
                $data['founder_photo_path'] = null;
                $this->oldFounderPhotoPath = null;
            }
        }

        $data['advantages_badge'] = $this->sanitizeString($data['advantages_badge'] ?? '');
        $data['advantages_title'] = $this->sanitizeString($data['advantages_title'] ?? '');
        $data['advantages_subtitle'] = $this->sanitizeNullableString($data['advantages_subtitle'] ?? null);
        $data['advantages_cta_text'] = $this->sanitizeString($data['advantages_cta_text'] ?? '');
        $data['advantages_cta_href'] = $this->sanitizeUrl($data['advantages_cta_href'] ?? '');
        $data['advantages_items'] = $this->normalizeAdvantagesItems($data['advantages_items'] ?? []);

        $data['faq_title'] = $this->sanitizeString($data['faq_title'] ?? '');
        $data['faq_subtitle'] = $this->sanitizeNullableString($data['faq_subtitle'] ?? null);
        $data['faq_items'] = $this->normalizeFaqItems($data['faq_items'] ?? []);

        $data['lessons_badge'] = $this->sanitizeString($data['lessons_badge'] ?? '');
        $data['lessons_title'] = $this->sanitizeString($data['lessons_title'] ?? '');
        $data['lessons_subtitle'] = $this->sanitizeNullableString($data['lessons_subtitle'] ?? null);
        $data['lessons_autoplay_on_view'] = filter_var($data['lessons_autoplay_on_view'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $data['lessons_videos'] = $this->normalizeLessonsVideos($data['lessons_videos'] ?? []);
        $data['vacancy_badge']    = $this->sanitizeString($data['vacancy_badge'] ?? '');
$data['vacancy_title']    = $this->sanitizeString($data['vacancy_title'] ?? '');
$data['vacancy_subtitle'] = $this->sanitizeNullableString($data['vacancy_subtitle'] ?? null);
$data['vacancy_bullets']  = $this->normalizeStringArray($data['vacancy_bullets'] ?? []);
$data['vacancy_cta_text'] = $this->sanitizeString($data['vacancy_cta_text'] ?? '');
$data['vacancy_cta_url']  = $this->sanitizeUrl($data['vacancy_cta_url'] ?? '');

$data['vacancy_media_url'] = $this->sanitizeNullableUrl($data['vacancy_media_url'] ?? null);

// локальне зображення → оптимізуємо до webp
if (array_key_exists('vacancy_media_path', $data)) {
    $new = is_string($data['vacancy_media_path']) ? trim($data['vacancy_media_path']) : null;
    if ($new) {
        $optimized = $this->optimizeAndReplacePublicImage($new, directory: 'vacancy', maxBytes: 3 * 1024 * 1024);
        if ($optimized) {
            if ($this->oldVacancyMediaPath && $this->oldVacancyMediaPath !== $optimized) {
                \Storage::disk('public')->delete($this->sanitizeRelative($this->oldVacancyMediaPath));
            }
            $data['vacancy_media_path'] = $optimized;
            $this->oldVacancyMediaPath = $optimized;
        }
    } else {
        if ($this->oldVacancyMediaPath) {
            \Storage::disk('public')->delete($this->sanitizeRelative($this->oldVacancyMediaPath));
        }
        $data['vacancy_media_path'] = null;
        $this->oldVacancyMediaPath = null;
    }
}
        $data['reviews_badge'] = $this->sanitizeString($data['reviews_badge'] ?? '');
        $data['reviews_title'] = $this->sanitizeString($data['reviews_title'] ?? '');
        $data['reviews_button_text'] = $this->sanitizeString($data['reviews_button_text'] ?? '');
        $data['reviews_button_url'] = $this->sanitizeUrl($data['reviews_button_url'] ?? 'https://instagram.com/your.profile');
        $data['reviews_items'] = $this->normalizeReviewItems($data['reviews_items'] ?? []);

        // 🖼️ якщо у формі є новий шлях — оптимізуємо і підміняємо на .webp, старий видаляємо
        if (array_key_exists('hero_image_path', $data)) {
            $newPath = is_string($data['hero_image_path']) ? trim($data['hero_image_path']) : null;

            if ($newPath) {
                $optimized = $this->optimizeAndReplacePublicImage($newPath, directory: 'homepage', maxBytes: 3 * 1024 * 1024);

                if ($optimized) {
                    // видаляємо старий лише якщо він відрізняється від нового
                    if ($this->oldHeroImagePath && $this->oldHeroImagePath !== $optimized) {
                        Storage::disk('public')->delete($this->sanitizeRelative($this->oldHeroImagePath));
                    }
                    $data['hero_image_path'] = $optimized;
                }
            } else {
                // якщо користувач прибрав зображення — гасимо старий файл
                if ($this->oldHeroImagePath) {
                    Storage::disk('public')->delete($this->sanitizeRelative($this->oldHeroImagePath));
                }
                $data['hero_image_path'] = null;
            }
        }

        return array_replace($defaults, $data);
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    protected function pricingCardSchema(string $basePath): array
    {
        return [
            TextInput::make($basePath . '.title')
                ->label('Заголовок картки')
                ->required(),

            TextInput::make($basePath . '.label')
                ->label('Підпис (мала літера)')
                ->maxLength(40),

            Textarea::make($basePath . '.description')
                ->label('Короткий опис')
                ->rows(2)
                ->columnSpanFull(),

            TextInput::make($basePath . '.price')
                ->label('Ціна за урок')
                ->required()
                ->inputMode('decimal')
                ->maxLength(10),

            TextInput::make($basePath . '.meta')
                ->label('Додаткова інформація (під ціною)')
                ->columnSpanFull(),

            TagsInput::make($basePath . '.features')
                ->label('Переваги')
                ->placeholder('Додайте пункт і натисніть Enter')
                ->reorderable()
                ->separator(',')
                ->afterStateHydrated(function ($set, $state) use ($basePath) {
                    if (is_string($state)) {
                        $set($basePath . '.features', preg_split('/\s*,\s*/', $state, -1, PREG_SPLIT_NO_EMPTY));
                    } elseif (! is_array($state)) {
                        $set($basePath . '.features', []);
                    }
                })
                ->dehydrateStateUsing(fn ($state) => $this->normalizeStringArray($state))
                ->columnSpanFull(),

            TextInput::make($basePath . '.cta_text')
                ->label('Текст кнопки')
                ->required(),

            TextInput::make($basePath . '.cta_href')
                ->label('Посилання для кнопки')
                ->required()
                ->maxLength(255),
        ];
    }

    protected function sanitizeString(mixed $value): string
    {
        return trim((string) ($value ?? ''));
    }

    protected function sanitizeCurrency(mixed $value): string
    {
        $currency = trim((string) ($value ?? ''));
        return $currency !== '' ? $currency : '₴';
    }

    protected function sanitizeNullableString(mixed $value): ?string
    {
        $str = trim((string) ($value ?? ''));
        return $str !== '' ? $str : null;
    }

    protected function sanitizePrice(mixed $value): string
    {
        if (is_numeric($value)) {
            $num = (float) $value;
            return rtrim(rtrim(number_format($num, 2, '.', ''), '0'), '.');
        }

        return trim((string) $value);
    }

    /**
     * @return array<int, string>
     */
    protected function normalizeStringArray(mixed $value): array
    {
        if (is_string($value)) {
            $value = preg_split('/\s*,\s*/', $value, -1, PREG_SPLIT_NO_EMPTY);
        }

        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_map(fn ($item) => is_string($item) ? trim($item) : '', $value), fn ($v) => $v !== ''));
    }

    protected function sanitizeUrl(mixed $value): string
    {
        $url = trim((string) ($value ?? ''));
        return $url !== '' ? $url : '#contact';
    }

    protected function sanitizeNullableUrl(mixed $value): ?string
    {
        $url = trim((string) ($value ?? ''));
        return $url !== '' ? $url : null;
    }

    /**
     * @return array<int, array{q: string, a: string}>
     */
    protected function normalizeFaqItems(mixed $items): array
    {
        if (! is_array($items)) {
            return app(HomePageSettings::class)->faq_items;
        }

        $normalized = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $question = $this->sanitizeString($item['q'] ?? '');
            $answer = trim((string) ($item['a'] ?? ''));

            if ($question === '' || $answer === '') {
                continue;
            }

            $normalized[] = [
                'q' => $question,
                'a' => $answer,
            ];
        }

        if ($normalized === []) {
            return app(HomePageSettings::class)->faq_items;
        }

        return $normalized;
    }

    /**
     * @return array<int, array{
     *     heading: string,
     *     body: array<int, string>,
     *     quote?: array{text: string, author: string},
     * }>
     */
    protected function normalizeFounderSections(mixed $sections): array
    {
        $defaults = app(HomePageSettings::class)->founder_sections;

        if (! is_array($sections)) {
            return $defaults;
        }

        $normalized = [];

        foreach ($sections as $section) {
            if (! is_array($section)) {
                continue;
            }

            $heading = $this->sanitizeString($section['heading'] ?? '');
            $body = $this->normalizeParagraphRepeater($section['body'] ?? []);

            if ($heading === '' || $body === []) {
                continue;
            }

            $record = [
                'heading' => $heading,
                'body' => $body,
            ];

            $quoteText = $this->sanitizeString($section['quote_text'] ?? ($section['quote']['text'] ?? ''));
            $quoteAuthor = $this->sanitizeString($section['quote_author'] ?? ($section['quote']['author'] ?? ''));

            if ($quoteText !== '') {
                $record['quote_text'] = $quoteText;
                $record['quote_author'] = $quoteAuthor;
            }

            $normalized[] = $record;
        }

        return $normalized !== [] ? $normalized : $defaults;
    }

    /**
     * @return array<int, array{heading: string, body: array<int, string>}>
     */
   protected function normalizeFounderExtras(mixed $sections): array
{
    if (! is_array($sections)) {
        return []; // дозволяємо відсутність додаткових розділів
    }

    $normalized = [];
    foreach ($sections as $section) {
        if (! is_array($section)) {
            continue;
        }

        $heading = $this->sanitizeString($section['heading'] ?? '');
        $body    = $this->normalizeParagraphRepeater($section['body'] ?? []);

        if ($heading === '' || $body === []) {
            continue;
        }

        $normalized[] = [
            'heading' => $heading,
            'body'    => $body,
        ];
    }

    return $normalized; // ніяких дефолтів, якщо користувач нічого не додав
}


    /**
     * @return array<int, string>
     */
   protected function normalizeParagraphRepeater(mixed $value): array
{
    if (! is_array($value)) {
        return [];
    }

    $paragraphs = [];

    foreach ($value as $item) {
        if (is_array($item) && array_key_exists('value', $item)) {
            $text = $this->sanitizeString($item['value']);
        } elseif (is_array($item) && array_key_exists('text', $item)) {
            $text = $this->sanitizeString($item['text']);
        } else {
            $text = $this->sanitizeString($item);
        }

        if ($text !== '') {
            $paragraphs[] = $text;
        }
    }

    // ❗ нове: прибираємо повні дублікати
    $paragraphs = array_values(array_unique($paragraphs));

    return $paragraphs;
}


    protected function prepareSectionsForForm(mixed $sections, bool $withQuote): array
    {
        if (! is_array($sections)) {
            return [];
        }

        $prepared = [];

        foreach ($sections as $section) {
            if (! is_array($section)) {
                continue;
            }

            $heading = $this->sanitizeString($section['heading'] ?? '');
            $body = $this->prepareParagraphItems($section['body'] ?? []);

            $record = [
                'heading' => $heading,
                'body' => $body,
            ];

            if ($withQuote) {
                $record['quote_text'] = $this->sanitizeString($section['quote_text'] ?? ($section['quote']['text'] ?? ''));
                $record['quote_author'] = $this->sanitizeString($section['quote_author'] ?? ($section['quote']['author'] ?? ''));
            }

            $prepared[] = $record;
        }

        return $prepared;
    }

    protected function prepareParagraphItems(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return array_map(function ($item) {
            if (is_array($item) && array_key_exists('value', $item)) {
                return ['value' => $this->sanitizeString($item['value'])];
            }

            if (is_string($item)) {
                return ['value' => $this->sanitizeString($item)];
            }

            return ['value' => ''];
        }, array_values($value));
    }

    protected function prepareReviewsForForm(mixed $items): array
    {
        if (! is_array($items)) {
            return [];
        }

        return array_map(function ($item) {
            $name = $this->sanitizeString($item['name'] ?? '');
            $course = $this->sanitizeString($item['course'] ?? '');
            $text = $this->sanitizeString($item['text'] ?? '');
            $stars = isset($item['stars']) ? (int) $item['stars'] : 5;

            $path = $item['avatar_path'] ?? null;
            $urlCandidate = $item['avatar_url'] ?? null;

            $avatarPath = (is_string($path) && $path !== '' && ! Str::startsWith($path, ['http://', 'https://', '/'])) ? $path : null;
            $avatarUrl = $avatarPath ? null : $this->sanitizeNullableUrl($urlCandidate ?? $path ?? null);

            return [
                'name' => $name,
                'course' => $course,
                'text' => $text,
                'stars' => $stars,
                'avatar_path' => $avatarPath,
                'avatar_url' => $avatarUrl,
            ];
        }, array_values($items));
    }

    /**
     * @return array<int, array{
     *     title: string,
     *     desc: ?string,
     *     bullets: array<int, string>,
     *     image_path: string,
     *     image_alt: string,
     *     icons: array<int, string>,
     * }>
     */
    protected function normalizeAdvantagesItems(mixed $items): array
    {
        $defaults = app(HomePageSettings::class)->advantages_items;

        if (! is_array($items)) {
            return $defaults;
        }

        $normalized = [];
        $newImages = [];
        $newIcons = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $title = $this->sanitizeString($item['title'] ?? '');
            if ($title === '') {
                continue;
            }

            $desc = $this->sanitizeNullableString($item['desc'] ?? null);
            $bullets = $this->normalizeStringArray($item['bullets'] ?? []);

            $imagePath = $this->processAdvantageImage($item['image_path'] ?? null);
            if ($imagePath === null) {
                continue;
            }
            if ($this->isPublicStoragePath($imagePath)) {
                $newImages[] = $imagePath;
            }

            $icons = [];
            $rawIcons = is_array($item['icons'] ?? null) ? $item['icons'] : [];
            foreach ($rawIcons as $icon) {
                $processedIcon = $this->processAdvantageIcon($icon);
                if ($processedIcon) {
                    $icons[] = $processedIcon;
                    if ($this->isPublicStoragePath($processedIcon)) {
                        $newIcons[] = $processedIcon;
                    }
                }
            }

            $normalized[] = [
                'title' => $title,
                'desc' => $desc,
                'bullets' => $bullets,
                'image_path' => $imagePath,
                'image_alt' => $this->sanitizeString($item['image_alt'] ?? ''),
                'icons' => $icons,
            ];
        }

        if ($normalized === []) {
            $normalized = $defaults;
            $newImages = [];
            $newIcons = [];
        }

        $this->cleanupAdvantageMedia($newImages, $newIcons);

        return $normalized;
    }

    /**
     * @return array<int, array{id: string, title: string, description: ?string}>
     */
    protected function normalizeLessonsVideos(mixed $videos): array
    {
        $defaults = app(HomePageSettings::class)->lessons_videos;

        if (! is_array($videos)) {
            return $defaults;
        }

        $normalized = [];

        foreach ($videos as $video) {
            if (! is_array($video)) {
                continue;
            }

            $idRaw = $video['id'] ?? '';
            $videoId = $this->extractYoutubeId($idRaw);
            if ($videoId === '') {
                continue;
            }

            $title = $this->sanitizeString($video['title'] ?? '');
            if ($title === '') {
                continue;
            }

            $description = $this->sanitizeNullableString($video['description'] ?? null);

            $normalized[] = [
                'id' => $videoId,
                'title' => $title,
                'description' => $description,
            ];
        }

        return $normalized !== [] ? $normalized : $defaults;
    }

    /**
     * @return array<int, array{
     *     name: string,
     *     text: string,
     *     course: ?string,
     *     stars: int,
     *     avatar_path: ?string,
     *     avatar_url: ?string,
     * }>
     */
    protected function normalizeReviewItems(mixed $items): array
    {
        $defaults = app(HomePageSettings::class)->reviews_items;

        if (! is_array($items)) {
            return $defaults;
        }

        $normalized = [];
        $newAvatars = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $name = $this->sanitizeString($item['name'] ?? '');
            $text = $this->sanitizeString($item['text'] ?? '');

            if ($name === '' || $text === '') {
                continue;
            }

            $course = $this->sanitizeString($item['course'] ?? '');
            $starsRaw = $item['stars'] ?? null;
            $stars = is_numeric($starsRaw) ? max(0, min(5, (int) $starsRaw)) : 5;

            $avatarPath = null;
            $avatarUrl = $this->sanitizeNullableUrl($item['avatar_url'] ?? null);

            $pathCandidate = is_string($item['avatar_path'] ?? null) ? trim($item['avatar_path']) : '';
            if ($pathCandidate !== '') {
                if ($this->isPublicStoragePath($pathCandidate)) {
                    $optimized = $this->optimizeAndReplacePublicImage($pathCandidate, directory: 'reviews/avatars', maxBytes: 1024 * 1024);
                    $avatarPath = $optimized ?? $pathCandidate;

                    if ($this->isPublicStoragePath($avatarPath)) {
                        $newAvatars[] = $avatarPath;
                    }
                    $avatarUrl = null;
                } elseif (Str::startsWith($pathCandidate, ['http://', 'https://', '/'])) {
                    $avatarUrl = $this->sanitizeNullableUrl($pathCandidate);
                    $avatarPath = null;
                }
            }

            $normalized[] = [
                'name' => $name,
                'text' => $text,
                'course' => $course !== '' ? $course : null,
                'stars' => $stars,
                'avatar_path' => $avatarPath,
                'avatar_url' => $avatarUrl,
            ];
        }

        if ($normalized === []) {
            $normalized = $defaults;
            $newAvatars = [];
        }

        $this->cleanupReviewAvatars($newAvatars);

        return $normalized;
    }

    protected function extractYoutubeId(mixed $value): string
    {
        $str = trim((string) ($value ?? ''));
        if ($str === '') {
            return '';
        }

        if (preg_match('~^[\w-]{11}$~', $str)) {
            return $str;
        }

        if (preg_match('~(?:youtube\.com/(?:watch\?v=|embed/|shorts/|v/)|youtu\.be/)([\w-]{11})~i', $str, $matches)) {
            return $matches[1];
        }

        parse_str(parse_url($str, PHP_URL_QUERY) ?? '', $query);
        if (! empty($query['v']) && preg_match('~^[\w-]{11}$~', $query['v'])) {
            return $query['v'];
        }

        return '';
    }

    protected function processAdvantageImage(mixed $path): ?string
    {
        if (! is_string($path)) {
            return null;
        }

        $trim = trim($path);
        if ($trim === '') {
            return null;
        }

        if (Str::startsWith($trim, ['http://', 'https://', '/'])) {
            return $trim;
        }

        $relative = $this->sanitizeRelative($trim);
        if ($relative === '') {
            return null;
        }

        return $this->optimizeAndReplacePublicImage($relative, directory: 'advantages', maxBytes: 3 * 1024 * 1024) ?? $relative;
    }

    protected function processAdvantageIcon(mixed $path): ?string
    {
        if (! is_string($path)) {
            return null;
        }

        $trim = trim($path);
        if ($trim === '') {
            return null;
        }

        if (Str::startsWith($trim, ['http://', 'https://', '/'])) {
            return $trim;
        }

        $relative = $this->sanitizeRelative($trim);
        if ($relative === '') {
            return null;
        }

        return $this->optimizeAndReplacePublicImage($relative, directory: 'advantages/icons', maxBytes: 1024 * 1024) ?? $relative;
    }

    protected function cleanupAdvantageMedia(array $newImages, array $newIcons): void
    {
        $disk = Storage::disk('public');

        $imagesToDelete = array_filter($this->oldAdvantageImages, fn ($path) => $this->isPublicStoragePath($path) && ! in_array($path, $newImages, true));
        if ($imagesToDelete) {
            $disk->delete($imagesToDelete);
        }

        $iconsToDelete = array_filter($this->oldAdvantageIcons, fn ($path) => $this->isPublicStoragePath($path) && ! in_array($path, $newIcons, true));
        if ($iconsToDelete) {
            $disk->delete($iconsToDelete);
        }

        $this->oldAdvantageImages = $newImages;
        $this->oldAdvantageIcons = $newIcons;
    }

    protected function cleanupReviewAvatars(array $newAvatars): void
    {
        $disk = Storage::disk('public');

        $toDelete = array_filter($this->oldReviewAvatars, fn ($path) => $this->isPublicStoragePath($path) && ! in_array($path, $newAvatars, true));
        if ($toDelete) {
            $disk->delete($toDelete);
        }

        $this->oldReviewAvatars = $newAvatars;
    }

    protected function isPublicStoragePath(string $path): bool
    {
        return $path !== '' && ! Str::startsWith($path, ['http://', 'https://', '/']);
    }

    /**
     * Конвертує public-файл у .webp з компресією та повертає новий відносний шлях.
     * Стирає оригінал лише після успішної конвертації.
     */
    protected function optimizeAndReplacePublicImage(string $path, string $directory = 'homepage', int $maxBytes = 3145728): ?string
    {
        $disk = Storage::disk('public');
        $path = $this->sanitizeRelative($path);
        if ($path === '' || ! $disk->exists($path)) {
            return null;
        }

        $abs = $disk->path($path);
        $ext = strtolower(pathinfo($abs, PATHINFO_EXTENSION));

        // вже webp — лише піджати до ліміту
        if ($ext === 'webp') {
            $this->ensureWebpUnderLimit($abs, $maxBytes);
            Log::debug('advantages:image already webp', ['path' => $path]);
            return $path;
        }

        if (! function_exists('imagewebp')) {
            Log::warning('advantages:imagewebp missing', ['path' => $path]);
            return $path; // без GD webp — віддаємо як є
        }

        $img = $this->createImageResource($abs, $ext);
        if (! $img) {
            Log::warning('advantages:createImageResource failed', ['path' => $path, 'ext' => $ext]);
            return $path;
        }

        // виправляємо орієнтацію для jpeg
        if (in_array($ext, ['jpg','jpeg'], true) && function_exists('exif_read_data')) {
            $img = $this->fixExifOrientation($abs, $img);
        }

        imagepalettetotruecolor($img);
        imagealphablending($img, true);
        imagesavealpha($img, true);

        $newPath = rtrim($directory, '/').'/'.Str::uuid()->toString().'.webp';
        $newAbs  = $disk->path($newPath);

        // первинна якість (78 — добрий баланс)
        if (! imagewebp($img, $newAbs, 78)) {
            imagedestroy($img);
            Log::warning('advantages:imagewebp convert failed', ['from' => $path, 'to' => $newPath]);
            return $path;
        }
        imagedestroy($img);

        // одна ітерація під ліміт
        $this->ensureWebpUnderLimit($newAbs, $maxBytes);

        // після успіху — стираємо оригінал
        if (! $disk->delete($path)) {
            @unlink($abs);
        }

        Log::debug('advantages:converted image', ['from' => $path, 'to' => $newPath]);

        return $newPath;
    }

    protected function sanitizeRelative(string $path): string
    {
        $p = ltrim((string) $path, '/\\');
        return str_contains($p, '..') ? '' : $p;
    }

    protected function createImageResource(string $absolute, string $ext)
    {
        // захист від надвеликих полотен
        $info = @getimagesize($absolute);
        if ($info && ($info[0] * $info[1] > 40_000_000)) {
            return null;
        }

        $resource = match ($ext) {
            'jpg','jpeg' => @imagecreatefromjpeg($absolute),
            'png'        => @imagecreatefrompng($absolute),
            'gif'        => @imagecreatefromgif($absolute),
            'webp'       => @imagecreatefromwebp($absolute),
            default      => false,
        };

        if (! $resource) {
            $contents = @file_get_contents($absolute);
            if ($contents !== false) {
                $resource = @imagecreatefromstring($contents);
            }
        }

        return $resource ?: null;
    }

    protected function fixExifOrientation(string $absolute, $image)
    {
        try {
            $exif = @exif_read_data($absolute);
            if (! $exif || empty($exif['Orientation'])) return $image;
            $o = (int) $exif['Orientation'];
            if ($o === 3) $image = imagerotate($image, 180, 0);
            if ($o === 6) $image = imagerotate($image, -90, 0);
            if ($o === 8) $image = imagerotate($image, 90, 0);
        } catch (\Throwable $e) {
            // ignore
        }
        return $image;
    }

    protected function ensureWebpUnderLimit(string $absolute, int $maxBytes = 3145728): void
    {
        if (! file_exists($absolute) || ! function_exists('imagecreatefromwebp')) return;

        $size = @filesize($absolute);
        if ($size !== false && $size <= $maxBytes) return;

        $img = @imagecreatefromwebp($absolute);
        if (! $img) return;

        // груба оцінка: якщо >6MB, одразу 68, інакше 74
        $q = ($size && $size > 6 * 1024 * 1024) ? 68 : 74;
        imagewebp($img, $absolute, $q);
        imagedestroy($img);
    }
}
