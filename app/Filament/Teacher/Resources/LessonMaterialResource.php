<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\LessonMaterialResource\Pages;
use App\Models\Lesson;
use App\Models\LessonMaterial;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LessonMaterialResource extends Resource
{
    protected static ?string $model = LessonMaterial::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-clip';
    protected static ?string $navigationLabel = 'Матеріали';
    protected static ?string $modelLabel = 'Матеріал';
    protected static ?string $pluralModelLabel = 'Матеріали';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('lesson_id')
                    ->label('Урок')
                    ->relationship('lesson', 'title', fn (Builder $query) => $query->where('teacher_id', Auth::id()))
                    ->preload()
                    ->searchable()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('title')
                            ->label('Назва уроку')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('topic')
                            ->label('Тема')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Короткий опис')
                            ->rows(3)
                            ->maxLength(1000),
                    ])
                    ->createOptionUsing(function (array $data) {
                        return Lesson::query()->create([
                            'teacher_id' => Auth::id(),
                            'title' => $data['title'],
                            'topic' => $data['topic'],
                            'description' => $data['description'] ?? null,
                            'status' => 'scheduled',
                        ])->getKey();
                    }),
                Forms\Components\TextInput::make('title')
                    ->label('Назва матеріалу')
                    ->required()
                    ->maxLength(255),
                Forms\Components\RichEditor::make('content')
                    ->label('Текст уроку')
                    ->columnSpanFull()
                    ->nullable(),
                Repeater::make('videos')
                    ->label('Відео / посилання')
                    ->schema([
                        Forms\Components\TextInput::make('label')
                            ->label('Назва')
                            ->maxLength(255)
                            ->nullable(),
                        Forms\Components\TextInput::make('url')
                            ->label('URL')
                            ->placeholder('https://youtu.be/...')
                            ->url()
                            ->required(),
                        Forms\Components\Textarea::make('note')
                            ->label('Опис')
                            ->rows(2)
                            ->nullable(),
                    ])
                    ->columnSpanFull()
                    ->collapsible()
                    ->createItemButtonLabel('Додати посилання')
                    ->default(fn (?LessonMaterial $record) => $record?->videos ?? ($record && $record->external_url ? [[
                        'label' => null,
                        'url' => $record->external_url,
                        'note' => null,
                    ]] : []))
                    ->nullable(),
                Forms\Components\FileUpload::make('images')
                    ->label('Зображення (до 3, максимум 3 МБ кожне)')
                    ->helperText('Фотографії автоматично стискаються і конвертуються у WebP.')
                    ->directory('materials/images')
                    ->disk('public')
                    ->image()
                    ->multiple()
                    ->maxFiles(3)
                    ->maxSize(3072)
                    ->reorderable()
                    ->visibility('public')
                    ->nullable()
                    ->columnSpanFull(),
                Repeater::make('resource_links')
                    ->label('Додаткові посилання')
                    ->schema([
                        Forms\Components\TextInput::make('label')
                            ->label('Назва')
                            ->maxLength(255)
                            ->required(),
                        Forms\Components\TextInput::make('url')
                            ->label('URL')
                            ->url()
                            ->required(),
                        Forms\Components\Textarea::make('note')
                            ->label('Опис')
                            ->rows(2)
                            ->nullable(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->defaultItems(0)
                    ->nullable(),
                Forms\Components\FileUpload::make('attachments')
                    ->label('Файли / зображення / PDF')
                    ->helperText('Можна додати кілька файлів.')
                    ->directory('materials/files')
                    ->disk('public')
                    ->preserveFilenames()
                    ->visibility('public')
                    ->multiple()
                    ->nullable()
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/zip',
                        'application/x-zip-compressed',
                    ])
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('thumbnail_path')
                    ->label('Превʼю (URL)')
                    ->helperText('Додайте посилання на зображення-превʼю, якщо потрібно.')
                    ->url()
                    ->maxLength(255)
                    ->nullable(),
                Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->label('Короткий опис'),
                Forms\Components\Toggle::make('is_downloadable')
                    ->label('Дозволити завантаження')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lesson.title')
                    ->label('Урок')
                    ->wrap()
                    ->sortable(),
                TagsColumn::make('content_types')
                    ->label('Вміст')
                    ->getStateUsing(function (LessonMaterial $record): array {
                        $tags = [];
                        if (! empty($record->content)) {
                            $tags[] = 'Текст';
                        }
                        if (! empty($record->videos) || ! empty($record->external_url)) {
                            $tags[] = 'Посилання';
                        }
                        if (! empty($record->resource_links)) {
                            $tags[] = 'Джерела';
                        }
                        if (! empty($record->videos) || ! empty($record->external_url)) {
                            $tags[] = 'Відео';
                        }
                        if (! empty($record->attachments)) {
                            $tags[] = 'Файли';
                        }
                        if (! empty($record->images)) {
                            $tags[] = 'Зображення';
                        }
                        if (! empty($record->thumbnail_path)) {
                            $tags[] = 'Превʼю';
                        }

                        return $tags ?: ['Матеріал'];
                    })
                    ->colors([
                        'primary',
                        'success',
                        'info',
                        'warning',
                    ]),
                Tables\Columns\TextColumn::make('lesson.topic')
                    ->label('Тема')
                    ->toggleable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Додано')
                    ->dateTime('d.m H:i')
                    ->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLessonMaterials::route('/'),
            'create' => Pages\CreateLessonMaterial::route('/create'),
            'edit' => Pages\EditLessonMaterial::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $teacherId = Auth::id() ?? 0;

        return parent::getEloquentQuery()
            ->whereHas('lesson', fn (Builder $query) => $query->where('teacher_id', $teacherId));
    }

    public static function getNavigationBadge(): ?string
    {
        $teacherId = Auth::id() ?? 0;

        return (string) static::getModel()::query()
            ->whereHas('lesson', fn (Builder $query) => $query->where('teacher_id', $teacherId))
            ->count();
    }
}
