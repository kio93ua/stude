<?php

namespace App\Filament\Admin\Pages;

use App\Settings\HomePageSettings;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\FileUpload;
use Filament\Pages\SettingsPage;
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

    public function mount(): void
    {
        parent::mount();
        // зчитуємо поточне значення перед редагуванням, щоб мати що видалити після апдейту
        $settings = app(HomePageSettings::class);
        $this->oldHeroImagePath = $settings->hero_image_path ?: null;
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
        ];
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
            return $path;
        }

        if (! function_exists('imagewebp')) {
            return $path; // без GD webp — віддаємо як є
        }

        $img = $this->createImageResource($abs, $ext);
        if (! $img) return $path;

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
            return $path;
        }
        imagedestroy($img);

        // одна ітерація під ліміт
        $this->ensureWebpUnderLimit($newAbs, $maxBytes);

        // після успіху — стираємо оригінал
        $disk->delete($path);

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

        return match ($ext) {
            'jpg','jpeg' => @imagecreatefromjpeg($absolute),
            'png'        => @imagecreatefrompng($absolute),
            'gif'        => @imagecreatefromgif($absolute),
            default      => @imagecreatefromstring(@file_get_contents($absolute)),
        };
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
