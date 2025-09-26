<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\StudyTestResource\Pages;
use App\Models\StudyTest;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class StudyTestResource extends Resource
{
    protected static ?string $model = StudyTest::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Тести';
    protected static ?string $modelLabel = 'Тест';
    protected static ?string $pluralModelLabel = 'Тести';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('teacher_id')->default(fn () => Auth::id()),
                Select::make('lesson_id')
                    ->label('Урок')
                    ->relationship('lesson', 'title', fn (Builder $query) => $query->where('teacher_id', Auth::id()))
                    ->searchable()
                    ->nullable(),
                TextInput::make('title')
                    ->label('Назва')
                    ->required()
                    ->maxLength(255),
                Select::make('category')
                    ->label('Категорія')
                    ->options([
                        'grammar' => 'Граматика',
                        'speaking' => 'Говоріння',
                        'listening' => 'Аудіювання',
                        'reading' => 'Читання',
                        'writing' => 'Письмо',
                        'general' => 'Загальні навички',
                    ])
                    ->default('general'),
                Textarea::make('description')
                    ->label('Опис')
                    ->rows(3),
                TextInput::make('link_url')
                    ->label('Посилання на ресурс')
                    ->maxLength(255)
                    ->url()
                    ->nullable(),
                TextInput::make('max_score')
                    ->label('Макс. бал')
                    ->numeric()
                    ->default(100),
                Grid::make(2)->schema([
                    Forms\Components\DateTimePicker::make('available_from')
                        ->label('Доступний з')
                        ->seconds(false)
                        ->nullable(),
                    Forms\Components\DateTimePicker::make('due_at')
                        ->label('Дедлайн')
                        ->seconds(false)
                        ->nullable(),
                ]),
                RichEditor::make('instructions')
                    ->label('Інструкції')
                    ->toolbarButtons(['bold', 'italic', 'bulletList', 'orderedList', 'link'])
                    ->columnSpanFull(),
                Repeater::make('questions')
                    ->label('Питання')
                    ->relationship('questions')
                    ->orderable('position')
                    ->minItems(1)
                    ->collapsed()
                    ->createItemButtonLabel('Додати питання')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('category')
                                ->label('Тема')
                                ->maxLength(100),
                            Select::make('type')
                                ->label('Тип')
                                ->options([
                                    'single_choice' => 'Один варіант',
                                    'multiple_choice' => 'Кілька варіантів',
                                    'text' => 'Відкрита відповідь',
                                ])
                                ->default('single_choice')
                                ->required(),
                            TextInput::make('points')
                                ->label('Бали')
                                ->numeric()
                                ->default(1)
                                ->minValue(0),
                        ]),
                        Textarea::make('prompt')
                            ->label('Запитання')
                            ->rows(2)
                            ->required(),
                        RichEditor::make('rich_content')
                            ->label('Додатковий текст')
                            ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'orderedList', 'blockquote'])
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('attachment_path')
                            ->label('Зображення / файл')
                            ->directory('tests/questions')
                            ->imagePreviewHeight('160')
                            ->maxSize(4096)
                            ->nullable(),
                        Repeater::make('options')
                            ->label('Варіанти відповіді')
                            ->relationship('options')
                            ->orderable('position')
                            ->defaultItems(0)
                            ->minItems(0)
                            ->columns(2)
                            ->hidden(fn (callable $get): bool => $get('type') === 'text')
                            ->schema([
                                TextInput::make('label')
                                    ->label('Текст варіанту')
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\FileUpload::make('attachment_path')
                                    ->label('Зображення')
                                    ->directory('tests/options')
                                    ->imagePreviewHeight('120')
                                    ->maxSize(4096)
                                    ->columnSpan(2)
                                    ->nullable(),
                                Toggle::make('is_correct')
                                    ->label('Правильний')
                                    ->columnSpan(1),
                                TextInput::make('feedback')
                                    ->label('Пояснення')
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->columns(1)
                    ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('questions_count')
                    ->counts('questions')
                    ->label('Питань')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Категорія')
                    ->badge()
                    ->colors([
                        'primary' => 'general',
                        'success' => 'speaking',
                        'warning' => 'grammar',
                        'info' => 'listening',
                        'danger' => 'reading',
                        'secondary' => 'writing',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'grammar' => 'Граматика',
                        'speaking' => 'Говоріння',
                        'listening' => 'Аудіювання',
                        'reading' => 'Читання',
                        'writing' => 'Письмо',
                        'general' => 'Загальні навички',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('due_at')
                    ->label('Дедлайн')
                    ->dateTime('d.m H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_score')
                    ->label('Макс. бал')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Категорія')
                    ->options([
                        'grammar' => 'Граматика',
                        'speaking' => 'Говоріння',
                        'listening' => 'Аудіювання',
                        'reading' => 'Читання',
                        'writing' => 'Письмо',
                        'general' => 'Загальні навички',
                    ]),
            ])
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
            'index' => Pages\ListStudyTests::route('/'),
            'create' => Pages\CreateStudyTest::route('/create'),
            'edit' => Pages\EditStudyTest::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $teacherId = Auth::id() ?? 0;

        return parent::getEloquentQuery()
            ->withCount('questions')
            ->where('teacher_id', $teacherId);
    }

    public static function getNavigationBadge(): ?string
    {
        $teacherId = Auth::id() ?? 0;

        return (string) static::getModel()::query()
            ->where('teacher_id', $teacherId)
            ->count();
    }
}
