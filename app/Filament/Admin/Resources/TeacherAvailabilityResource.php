<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TeacherAvailabilityResource\Pages;
use App\Models\TeacherAvailability;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TeacherAvailabilityResource extends Resource
{
    protected static ?string $model = TeacherAvailability::class;

    protected static ?string $navigationGroup = 'Команда';

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Графік викладачів';

    protected static ?string $pluralModelLabel = 'Графік викладачів';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('teacher_id')
                    ->label('Викладач')
                    ->relationship('teacher', 'username')
                    ->getOptionLabelFromRecordUsing(fn (User $record) => $record->full_name ?: $record->username)
                    ->searchable()
                    ->required(),
                Select::make('weekday')
                    ->label('День тижня')
                    ->options(self::weekdayOptions())
                    ->required(),
                TimePicker::make('starts_at')
                    ->label('Початок')
                    ->seconds(false)
                    ->required(),
                TimePicker::make('ends_at')
                    ->label('Кінець')
                    ->seconds(false)
                    ->required()
                    ->after('starts_at'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Активний слот')
                    ->default(true),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('teacher.full_name')
                    ->label('Викладач')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('weekday')
                    ->label('День')
                    ->formatStateUsing(fn ($state) => self::weekdayOptions()[$state] ?? $state)
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->label('Початок')
                    ->time('H:i'),
                TextColumn::make('ends_at')
                    ->label('Кінець')
                    ->time('H:i'),
                BadgeColumn::make('is_active')
                    ->label('Статус')
                    ->alignCenter()
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ])
                    ->formatStateUsing(fn ($state) => $state ? 'Активний' : 'Неактивний'),
            ])
            ->filters([
                SelectFilter::make('teacher_id')
                    ->label('Викладач')
                    ->relationship('teacher', 'username', modifyQueryUsing: fn ($query) => $query->where('role', 'teacher'))
                    ->getOptionLabelFromRecordUsing(fn (User $record) => $record->full_name ?: $record->username),
                SelectFilter::make('weekday')
                    ->label('День тижня')
                    ->options(self::weekdayOptions()),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeacherAvailabilities::route('/'),
            'create' => Pages\CreateTeacherAvailability::route('/create'),
            'edit' => Pages\EditTeacherAvailability::route('/{record}/edit'),
        ];
    }

    public static function weekdayOptions(): array
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
}
