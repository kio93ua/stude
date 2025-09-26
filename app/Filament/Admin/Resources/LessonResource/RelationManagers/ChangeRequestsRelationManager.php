<?php

namespace App\Filament\Admin\Resources\LessonResource\RelationManagers;

use App\Models\LessonChangeRequest;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Carbon;

class ChangeRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'changeRequests';

    protected static ?string $recordTitleAttribute = 'type';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                ToggleButtons::make('type')
                    ->label('Тип')
                    ->options([
                        'reschedule' => 'Перенести',
                        'cancel' => 'Скасувати',
                        'assign_teacher' => 'Викладач',
                        'other' => 'Інше',
                    ])
                    ->inline()
                    ->required(),
                ToggleButtons::make('status')
                    ->label('Статус')
                    ->options([
                        'pending' => 'Очікує',
                        'approved' => 'Схвалено',
                        'rejected' => 'Відхилено',
                    ])
                    ->inline()
                    ->required(),
                Select::make('requested_by_id')
                    ->label('Ініціатор')
                    ->relationship('requester', 'username')
                    ->searchable()
                    ->default(auth()->id())
                    ->required(),
                DateTimePicker::make('proposed_starts_at')
                    ->label('Запропонований час')
                    ->seconds(false),
                Textarea::make('comment')
                    ->label('Коментар')
                    ->rows(3),
                Textarea::make('response_comment')
                    ->label('Відповідь адміністратора')
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
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
                    }),
                TextColumn::make('proposed_starts_at')
                    ->label('Запропонований час')
                    ->dateTime('d.m H:i'),
                TextColumn::make('requester.full_name')
                    ->label('Ініціатор')
                    ->formatStateUsing(fn ($state, LessonChangeRequest $record) => $record->requester?->full_name ?: $record->requester?->username),
                TextColumn::make('handled_by.full_name')
                    ->label('Опрацював')
                    ->formatStateUsing(fn ($state, LessonChangeRequest $record) => $record->handler?->full_name ?: $record->handler?->username),
                TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime('d.m.Y H:i'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        if (($data['status'] ?? 'pending') !== 'pending') {
                            $data['handled_by_id'] = $data['handled_by_id'] ?? auth()->id();
                            $data['handled_at'] = $data['handled_at'] ?? Carbon::now();
                        }

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        if (($data['status'] ?? 'pending') !== 'pending') {
                            $data['handled_by_id'] = $data['handled_by_id'] ?? auth()->id();
                            $data['handled_at'] = $data['handled_at'] ?? Carbon::now();
                        }

                        return $data;
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
