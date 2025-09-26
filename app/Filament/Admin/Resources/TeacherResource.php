<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TeacherResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TeacherResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Викладачі';
    protected static ?string $modelLabel = 'Викладач';
    protected static ?string $pluralModelLabel = 'Викладачі';
    protected static ?string $navigationGroup = 'Команда';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Облікові дані')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->label("Ім'я")
                            ->maxLength(255)
                            ->required(),
                        Forms\Components\TextInput::make('last_name')
                            ->label('Прізвище')
                            ->maxLength(255)
                            ->required(),
                        Forms\Components\TextInput::make('username')
                            ->label('Логін')
                            ->maxLength(255)
                            ->required()
                            ->rule('alpha_dash')
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->nullable(),
                        Forms\Components\TextInput::make('password')
                            ->label('Пароль')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8)
                            ->dehydrated(fn ($state): bool => filled($state)),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Підтвердження паролю')
                            ->password()
                            ->revealable()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->same('password')
                            ->dehydrated(false),
                        Forms\Components\Hidden::make('role')
                            ->default('teacher')
                            ->dehydrated(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')
                    ->label('Логін')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('ПІБ')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(query: fn (Builder $query, string $direction) => $query->orderBy('last_name', $direction)->orderBy('first_name', $direction))
                    ->formatStateUsing(fn ($state, User $record) => $record->full_name ?: '—'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('teaching_groups_count')
                    ->label('Груп')
                    ->counts('teachingGroups')
                    ->sortable(),
                Tables\Columns\TextColumn::make('taught_lessons_count')
                    ->label('Уроків створено')
                    ->counts('taughtLessons')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('recent')
                    ->label('Створені за останні 30 днів')
                    ->query(fn (Builder $query) => $query->where('created_at', '>=', now()->subDays(30))),
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
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', 'teacher');
    }

    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['role'] = 'teacher';
        $data['name'] = trim(collect([$data['first_name'] ?? null, $data['last_name'] ?? null])->filter()->join(' '));

        return $data;
    }

    protected static function mutateFormDataBeforeSave(array $data): array
    {
        $data['role'] = 'teacher';
        $data['name'] = trim(collect([$data['first_name'] ?? null, $data['last_name'] ?? null])->filter()->join(' '));

        return $data;
    }
}
