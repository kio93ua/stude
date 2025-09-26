<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\HomeworkResource\Pages;
use App\Models\Homework;
use App\Models\Lesson;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class HomeworkResource extends Resource
{
    protected static ?string $model = Homework::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Домашні завдання';
    protected static ?string $modelLabel = 'Домашнє завдання';
    protected static ?string $pluralModelLabel = 'Домашні завдання';

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
                    ->label('Назва завдання')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Короткий опис')
                    ->rows(3)
                    ->columnSpanFull()
                    ->nullable(),
                RichEditor::make('content')
                    ->label('Інструкція / текст завдання')
                    ->columnSpanFull()
                    ->nullable(),
                Forms\Components\DateTimePicker::make('due_at')
                    ->label('Дедлайн')
                    ->seconds(false)
                    ->nullable(),
                Forms\Components\Select::make('students')
                    ->label('Призначити студентам')
                    ->relationship('students', 'name')
                    ->multiple()
                    ->preload(false)
                    ->searchable()
                    ->options(function (Get $get) {
                        $lessonId = $get('lesson_id');
                        $selected = collect($get('students') ?? []);

                        $query = User::query()->where('role', 'student');

                        if ($lessonId) {
                            $query->whereHas('lessonEnrollments', fn (Builder $subQuery) => $subQuery->where('lesson_id', $lessonId));
                        }

                        $options = $query->orderBy('name')
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn (User $user) => [$user->getKey() => trim($user->full_name . ' · ' . $user->email)]);

                        if ($selected->isNotEmpty()) {
                            $selectedUsers = User::query()
                                ->whereIn('id', $selected)
                                ->get()
                                ->mapWithKeys(fn (User $user) => [$user->getKey() => trim($user->full_name . ' · ' . $user->email)]);

                            $options = $selectedUsers->union($options);
                        }

                        return $options->toArray();
                    })
                    ->getSearchResultsUsing(function (string $search, Get $get) {
                        $lessonId = $get('lesson_id');

                        return User::query()
                            ->where('role', 'student')
                            ->when($lessonId, fn (Builder $query) => $query->whereHas('lessonEnrollments', fn (Builder $subQuery) => $subQuery->where('lesson_id', $lessonId)))
                            ->where(function (Builder $query) use ($search) {
                                $query->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            })
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn (User $user) => [$user->getKey() => trim($user->full_name . ' · ' . $user->email)])
                            ->toArray();
                    })
                    ->getOptionLabelFromRecordUsing(fn (User $record) => trim($record->full_name . ' · ' . $record->email))
                    ->helperText('Оберіть одного або кількох учнів. Список фільтрується за обраним уроком.')
                    ->columnSpanFull(),
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
                    ->default(fn (?Homework $record) => $record?->videos ?? []),
                Forms\Components\FileUpload::make('images')
                    ->label('Зображення (до 3, максимум 3 МБ)')
                    ->helperText('Фото автоматично стискаються та конвертуються у WebP.')
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
                    ->columnSpanFull()
                    ->collapsible()
                    ->defaultItems(0)
                    ->nullable(),
                Forms\Components\FileUpload::make('attachments')
                    ->label('Файли / документи')
                    ->helperText('DOC, PDF, XLS, ZIP тощо.')
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
                Forms\Components\Toggle::make('is_downloadable')
                    ->label('Дозволити завантаження файлів')
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
                Tables\Columns\TextColumn::make('due_at')
                    ->label('Дедлайн')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('submissions_count')
                    ->counts('submissions')
                    ->label('Отримано відповідей')
                    ->sortable(),
                TagsColumn::make('content')
                    ->label('Вміст')
                    ->getStateUsing(function (Homework $record): array {
                        $tags = [];
                        if (! empty($record->content)) {
                            $tags[] = 'Текст';
                        }
                        if (! empty($record->videos)) {
                            $tags[] = 'Відео';
                        }
                        if (! empty($record->resource_links)) {
                            $tags[] = 'Джерела';
                        }
                        if (! empty($record->attachments)) {
                            $tags[] = 'Файли';
                        }
                        if (! empty($record->images)) {
                            $tags[] = 'Зображення';
                        }

                        return $tags ?: ['Завдання'];
                    }),
                Tables\Columns\TextColumn::make('students_count')
                    ->counts('students')
                    ->label('Кому призначено')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime('d.m H:i')
                    ->sortable(),
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
            'index' => Pages\ListHomeworks::route('/'),
            'create' => Pages\CreateHomework::route('/create'),
            'edit' => Pages\EditHomework::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $teacherId = Auth::id() ?? 0;

        return parent::getEloquentQuery()
            ->where('teacher_id', $teacherId)
            ->withCount('students');
    }
}
