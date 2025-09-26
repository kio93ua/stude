<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LessonResource\Pages;
use App\Filament\Admin\Resources\LessonResource\RelationManagers\ChangeRequestsRelationManager;
use App\Models\Lesson;
use App\Models\StudyGroup;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class LessonResource extends Resource
{
    protected static ?string $model = Lesson::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Уроки';
    protected static ?string $pluralModelLabel = 'Уроки';
    protected static ?string $modelLabel = 'Урок';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Учасники')
                    ->schema([
                        Select::make('format')
                            ->label('Формат')
                            ->options([
                                'individual' => 'Індивідуальний урок',
                                'group'      => 'Групове заняття',
                            ])
                            ->default('individual')
                            ->reactive(),

                        Select::make('teacher_id')
                            ->label('Викладач')
                            ->relationship('teacher', 'username', modifyQueryUsing: fn ($query) => $query->where('role', 'teacher'))
                            ->getOptionLabelFromRecordUsing(fn (User $record) => $record->full_name ?: $record->username)
                            ->searchable()
                            ->required(),

                        Select::make('group_id')
                            ->label('Навчальна група')
                            ->options(fn () => static::groupOptions())
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->visible(fn (callable $get) => $get('format') === 'group')
                            ->afterStateUpdated(function (callable $set, ?string $state): void {
                                if (! $state) {
                                    return;
                                }

                                $group = StudyGroup::query()->with('students')->find($state);

                                if ($group) {
                                    $set('student_ids', $group->students->pluck('id')->all());
                                }
                            })
                            ->hint('Склад групи автоматично підставляється у список учасників.'),

                        Select::make('student_ids')
                            ->label('Учні')
                            ->multiple()
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                return User::query()
                                    ->select(['id', 'first_name', 'last_name', 'username'])
                                    ->where('role', 'student')
                                    ->when($search !== '', function ($query) use ($search) {
                                        $query->where(function ($query) use ($search) {
                                            $query->where('username', 'like', "%{$search}%")
                                                ->orWhereRaw("CONCAT(COALESCE(first_name,''),' ',COALESCE(last_name,'')) LIKE ?", ["%{$search}%"]);
                                        });
                                    })
                                    ->orderBy('last_name')
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn (User $student) => [
                                        $student->id => $student->full_name ?: $student->username,
                                    ])
                                    ->all();
                            })
                            ->getOptionLabelsUsing(function (array $values) {
                                return User::query()
                                    ->select(['id', 'first_name', 'last_name', 'username'])
                                    ->whereIn('id', $values)
                                    ->get()
                                    ->mapWithKeys(fn (User $student) => [
                                        $student->id => $student->full_name ?: $student->username,
                                    ])
                                    ->all();
                            })
                            ->visible(fn (callable $get) => $get('format') !== 'group')
                            ->afterStateHydrated(function (Select $component, ?Lesson $record): void {
                                if ($record) {
                                    $component->state($record->students->pluck('id')->all());
                                }
                            })
                            ->helperText('Обрати одного або кількох учнів для заняття.'),
                    ])->columns(2),

                Section::make('Планування')
                    ->schema([
                        Toggle::make('create_series')
                            ->label('Створити повторювану серію')
                            ->helperText('Система створить серію на основі вибраних параметрів.')
                            ->reactive()
                            ->hidden(fn (?Lesson $record) => filled($record)),
                        DateTimePicker::make('starts_at')
                            ->label('Дата та час')
                            ->seconds(false)
                            ->required(fn (callable $get) => ! $get('create_series'))
                            ->visible(fn (callable $get) => ! $get('create_series')),
                        Select::make('series_weekday')
                            ->label('День тижня')
                            ->options(static::weekdayOptions())
                            ->required(fn (callable $get) => (bool) $get('create_series'))
                            ->visible(fn (callable $get, ?Lesson $record) => $record === null && $get('create_series')),
                        DatePicker::make('series_starts_on')
                            ->label('Початок серії')
                            ->required(fn (callable $get) => (bool) $get('create_series'))
                            ->visible(fn (callable $get, ?Lesson $record) => $record === null && $get('create_series')),
                        DatePicker::make('series_ends_on')
                            ->label('Завершення серії')
                            ->minDate(fn (callable $get) => $get('series_starts_on'))
                            ->visible(fn (callable $get, ?Lesson $record) => $record === null && $get('create_series')),
                        TimePicker::make('series_time')
                            ->label('Час заняття')
                            ->seconds(false)
                            ->required(fn (callable $get) => (bool) $get('create_series'))
                            ->visible(fn (callable $get, ?Lesson $record) => $record === null && $get('create_series')),
                        TextInput::make('duration_minutes')
                            ->label('Тривалість (хв)')
                            ->numeric()
                            ->minValue(15)
                            ->default(60)
                            ->required(),
                        TextInput::make('meeting_url')
                            ->label('Посилання на урок')
                            ->url()
                            ->maxLength(255),
                        Select::make('status')
                            ->label('Статус')
                            ->options([
                                'scheduled'   => 'Заплановано',
                                'in_progress' => 'В процесі',
                                'completed'   => 'Завершено',
                                'cancelled'   => 'Скасовано',
                            ])
                            ->default('scheduled')
                            ->required(),
                        Toggle::make('is_temporary')
                            ->label('Тимчасове заняття')
                            ->helperText('Позначте, якщо урок разовий або позаплановий навіть у межах серії.'),
                    ])->columns(2),

                Section::make('Опис')
                    ->schema([
                        TextInput::make('title')
                            ->label('Назва')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Опис')
                            ->rows(4),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn ($query) => $query->with(['teacher', 'group'])->withCount('students')
            )
            ->columns([
                IconColumn::make('series_id')
                    ->label('Серія')
                    ->boolean()
                    ->trueIcon('heroicon-o-arrow-path')
                    ->falseIcon('heroicon-o-minus'),
                TextColumn::make('title')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('teacher.full_name')
                    ->label('Викладач')
                    ->formatStateUsing(fn ($state, Lesson $record) => $record->teacher?->full_name ?: $record->teacher?->username)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('format')
                    ->label('Формат')
                    ->formatStateUsing(fn ($state) => $state === 'group' ? 'Груповий' : 'Індивідуальний')
                    ->badge()
                    ->colors([
                        'primary' => ['individual'],
                        'warning' => ['group'],
                    ])
                    ->sortable(),
                TextColumn::make('group.name')
                    ->label('Група')
                    ->toggleable()
                    ->formatStateUsing(fn ($state, Lesson $record) => $state ?? ($record->format === 'group' ? '—' : null))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('students_count')
                    ->label('Учнів')
                    ->badge()
                    ->sortable(),
                TextColumn::make('scheduled_for')
                    ->label('Дата')
                    ->sortable()
                    ->formatStateUsing(fn ($state, Lesson $record) => $state ? Carbon::parse($state)->format('d.m') : optional($record->starts_at)->format('d.m')),
                TextColumn::make('starts_at')
                    ->label('Час')
                    ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('H:i') : null),
                TextColumn::make('duration_minutes')
                    ->label('Хвилини')
                    ->sortable(),
                BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'primary' => 'scheduled',
                        'warning' => 'in_progress',
                        'success' => 'completed',
                        'danger'  => 'cancelled',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'scheduled'   => 'Заплановано',
                        'in_progress' => 'В процесі',
                        'completed'   => 'Завершено',
                        'cancelled'   => 'Скасовано',
                        default       => $state,
                    }),
                IconColumn::make('is_temporary')
                    ->label('Тимчасовий')
                    ->boolean()
                    ->trueIcon('heroicon-o-clock')
                    ->falseIcon('heroicon-o-check')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'scheduled'   => 'Заплановано',
                        'in_progress' => 'В процесі',
                        'completed'   => 'Завершено',
                        'cancelled'   => 'Скасовано',
                    ]),
                SelectFilter::make('teacher_id')
                    ->label('Викладач')
                    ->relationship('teacher', 'username', modifyQueryUsing: fn ($query) => $query->where('role', 'teacher'))
                    ->getOptionLabelFromRecordUsing(fn (User $record) => $record->full_name ?: $record->username),
                SelectFilter::make('format')
                    ->label('Формат')
                    ->options([
                        'individual' => 'Індивідуальний',
                        'group'      => 'Груповий',
                    ]),
                SelectFilter::make('group_id')
                    ->label('Група')
                    ->relationship('group', 'name')
                    ->searchable(),
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
        return [
            ChangeRequestsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListLessons::route('/'),
            'create' => Pages\CreateLesson::route('/create'),
            'edit'   => Pages\EditLesson::route('/{record}/edit'),
        ];
    }

    protected static function weekdayOptions(): array
    {
        return [
            0 => 'Понеділок',
            1 => 'Вівторок',
            2 => 'Середа',
            3 => 'Четвер',
            4 => 'Пʼятниця',
            5 => 'Субота',
            6 => 'Неділя',
        ];
    }

    protected static function studentOptions(): array
    {
        return User::query()
            ->where('role', 'student')
            ->orderBy('last_name')
            ->get()
            ->mapWithKeys(fn (User $student) => [$student->id => $student->full_name ?: $student->username])
            ->toArray();
    }

    protected static function groupOptions(): array
    {
        return StudyGroup::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->withCount('students')
            ->get()
            ->mapWithKeys(function (StudyGroup $group) {
                $label = $group->name;

                if ($group->students_count) {
                    $label .= ' (' . $group->students_count . ')';
                }

                return [$group->id => $label];
            })
            ->toArray();
    }
}
