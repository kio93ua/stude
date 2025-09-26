<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Користувачі';
    protected static ?string $modelLabel = 'Користувач';
    protected static ?string $pluralModelLabel = 'Користувачі';
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Облікові дані')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label("Ім'я")
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
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('role')
                            ->label('Роль')
                            ->required()
                            ->default('student')
                            ->options([
                                'admin' => 'Адмін',
                                'teacher' => 'Викладач',
                                'student' => 'Студент',
                            ]),
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
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('username')
                    ->label('Логін')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label("Ім'я")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('role')
                    ->label('Роль')
                    ->colors([
                        'primary' => 'admin',
                        'success' => 'teacher',
                        'warning' => 'student',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => 'Адмін',
                        'teacher' => 'Викладач',
                        'student' => 'Студент',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Створено')
                    ->dateTime('d.m.Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Роль')
                    ->options([
                        'admin' => 'Адмін',
                        'teacher' => 'Викладач',
                        'student' => 'Студент',
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
