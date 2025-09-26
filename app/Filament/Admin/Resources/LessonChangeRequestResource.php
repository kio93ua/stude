<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\LessonChangeRequestResource\Pages;
use App\Models\Lesson;
use App\Models\LessonChangeRequest;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class LessonChangeRequestResource extends Resource
{
    protected static ?string $model = LessonChangeRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'Розклад';

    protected static ?string $navigationLabel = 'Запити на зміни';

    protected static ?string $pluralModelLabel = 'Запити на зміни уроків';

    protected static ?int $navigationSort = 15;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Запит')
                    ->schema([
                        Select::make('lesson_id')
                            ->label('Урок')
                            ->relationship('lesson', 'title')
                            ->getOptionLabelFromRecordUsing(fn (Lesson $lesson) => $lesson->title . ' · ' . optional($lesson->starts_at)->format('d.m H:i'))
                            ->searchable()
                            ->required(),
                        ToggleButtons::make('type')
                            ->label('Тип запиту')
                            ->options([
                                'reschedule' => 'Перенести',
                                'cancel' => 'Скасувати',
                                'assign_teacher' => 'Змінити викладача',
                                'other' => 'Інше',
                            ])
                            ->inline()
                            ->required(),
                        Select::make('requested_by_id')
                            ->label('Хто ініціює')
                            ->relationship('requester', 'username')
                            ->getOptionLabelFromRecordUsing(fn (User $user) => $user->full_name ?: $user->username)
                            ->searchable()
                            ->required(),
                        DateTimePicker::make('proposed_starts_at')
                            ->label('Запропонований час')
                            ->seconds(false),
                        TextInput::make('proposed_duration')
                            ->label('Тривалість (хв)')
                            ->numeric()
                            ->minValue(15)
                            ->maxValue(240)
                            ->helperText('Залиште порожнім, якщо не змінюється.'),
                        Textarea::make('comment')
                            ->label('Коментар ініціатора')
                            ->rows(4),
                    ])->columns(2),
                Forms\Components\Section::make('Розгляд')
                    ->schema([
                        ToggleButtons::make('status')
                            ->label('Статус')
                            ->options([
                                'pending' => 'Очікує',
                                'approved' => 'Схвалено',
                                'rejected' => 'Відхилено',
                            ])
                            ->inline()
                            ->required(),
                        Select::make('handled_by_id')
                            ->label('Хто розглянув')
                            ->relationship('handler', 'username')
                            ->getOptionLabelFromRecordUsing(fn (User $user) => $user->full_name ?: $user->username)
                            ->searchable(),
                        DateTimePicker::make('handled_at')
                            ->label('Дата розгляду')
                            ->seconds(false),
                        Textarea::make('response_comment')
                            ->label('Коментар адміністратора')
                            ->rows(4),
                        Textarea::make('payload')
                            ->label('Додаткові дані (JSON)')
                            ->rows(3)
                            ->helperText('Наприклад, {"students":[1,2]} для пакетних змін.')
                            ->afterStateHydrated(fn (Textarea $component, $state) => $component->state($state ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : null))
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('lesson.title')
                    ->label('Урок')
                    ->limit(30)
                    ->searchable(),
                TextColumn::make('lesson.starts_at')
                    ->label('Поточний час')
                    ->dateTime('d.m H:i')
                    ->sortable(),
                BadgeColumn::make('type')
                    ->label('Тип')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'reschedule' => 'Перенесення',
                        'cancel' => 'Скасування',
                        'assign_teacher' => 'Викладач',
                        default => 'Інше',
                    })
                    ->colors([
                        'warning' => ['reschedule'],
                        'danger' => ['cancel'],
                        'primary' => ['assign_teacher'],
                        'gray' => ['other'],
                    ]),
                BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors([
                        'warning' => ['pending'],
                        'success' => ['approved'],
                        'danger' => ['rejected'],
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'Очікує',
                        'approved' => 'Схвалено',
                        'rejected' => 'Відхилено',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('proposed_starts_at')
                    ->label('Запропонований час')
                    ->dateTime('d.m H:i')
                    ->toggleable(),
                TextColumn::make('requester.full_name')
                    ->label('Ініціатор')
                    ->formatStateUsing(fn ($state, LessonChangeRequest $record) => $record->requester?->full_name ?: $record->requester?->username)
                    ->searchable(),
                TextColumn::make('handled_by.full_name')
                    ->label('Опрацював')
                    ->formatStateUsing(fn ($state, LessonChangeRequest $record) => $record->handler?->full_name ?: $record->handler?->username)
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'Очікує',
                        'approved' => 'Схвалено',
                        'rejected' => 'Відхилено',
                    ]),
                SelectFilter::make('type')
                    ->label('Тип')
                    ->options([
                        'reschedule' => 'Перенести',
                        'cancel' => 'Скасувати',
                        'assign_teacher' => 'Змінити викладача',
                        'other' => 'Інше',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListLessonChangeRequests::route('/'),
            'create' => Pages\CreateLessonChangeRequest::route('/create'),
            'edit' => Pages\EditLessonChangeRequest::route('/{record}/edit'),
        ];
    }
}
