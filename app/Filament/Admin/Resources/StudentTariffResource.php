<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StudentTariffResource\Pages;
use App\Models\StudentTariff;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class StudentTariffResource extends Resource
{
    protected static ?string $model = StudentTariff::class;

    protected static ?string $navigationGroup = 'Фінанси';

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Тарифи учнів';

    protected static ?string $pluralModelLabel = 'Тарифи учнів';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('student_id')
                    ->label('Учень')
                    ->relationship('student', 'username', modifyQueryUsing: fn ($query) => $query->where('role', 'student'))
                    ->getOptionLabelFromRecordUsing(fn (User $record) => $record->full_name ?: $record->username)
                    ->searchable()
                    ->required(),
                TextInput::make('price_per_lesson')
                    ->label('Ціна за урок')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
                TextInput::make('currency')
                    ->label('Валюта')
                    ->maxLength(3)
                    ->default('UAH')
                    ->required()
                    ->formatStateUsing(fn ($state) => Str::upper($state ?? 'UAH')),
                DatePicker::make('starts_on')
                    ->label('Початок дії')
                    ->required(),
                DatePicker::make('ends_on')
                    ->label('Завершення')
                    ->minDate(fn (callable $get) => $get('starts_on')),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.full_name')
                    ->label('Учень')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('price_per_lesson')
                    ->label('Ціна')
                    ->money(fn ($record) => $record->currency),
                TextColumn::make('starts_on')
                    ->label('Початок')
                    ->date(),
                TextColumn::make('ends_on')
                    ->label('Завершення')
                    ->date(),
                TextColumn::make('created_at')
                    ->label('Створено')
                    ->since(),
            ])
            ->filters([
                SelectFilter::make('student_id')
                    ->label('Учень')
                    ->relationship('student', 'username', modifyQueryUsing: fn ($query) => $query->where('role', 'student'))
                    ->getOptionLabelFromRecordUsing(fn (User $record) => $record->full_name ?: $record->username),
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
            'index' => Pages\ListStudentTariffs::route('/'),
            'create' => Pages\CreateStudentTariff::route('/create'),
            'edit' => Pages\EditStudentTariff::route('/{record}/edit'),
        ];
    }

}
