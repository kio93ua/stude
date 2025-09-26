<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\StudyGroupResource\Pages;
use App\Models\StudyGroup;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudyGroupResource extends Resource
{
    protected static ?string $model = StudyGroup::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Навчальні групи';
    protected static ?string $pluralModelLabel = 'Навчальні групи';
    protected static ?string $modelLabel = 'Група';
    protected static ?string $navigationGroup = 'Команда';
    protected static ?int $navigationSort = 15;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Основна інформація')
                    ->schema([
                        TextInput::make('name')
                            ->label('Назва')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Опис')
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Активна')
                            ->default(true),
                    ])->columns(2),
                Forms\Components\Section::make('Склад групи')
                    ->schema([
                        Select::make('primary_teacher_id')
                            ->label('Основний викладач')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search) => self::searchTeachers($search))
                            ->getOptionLabelUsing(fn ($value) => $value ? self::teacherLabel((int) $value) : null)
                            ->placeholder('Оберіть викладача'),
                        Select::make('teachers')
                            ->label('Інші викладачі')
                            ->relationship('teachers', 'username', modifyQueryUsing: fn ($query) => $query->where('role', 'teacher'))
                            ->getOptionLabelFromRecordUsing(fn (User $record) => $record->full_name ?: $record->username)
                            ->multiple()
                            ->searchable()
                            ->preload(false)
                            ->getSearchResultsUsing(fn (string $search) => self::searchTeachers($search))
                            ->getOptionLabelsUsing(fn (array $values) => self::teacherLabels($values)),
                        Select::make('students')
                            ->label('Учні')
                            ->relationship('students', 'username', modifyQueryUsing: fn ($query) => $query->where('role', 'student'))
                            ->getOptionLabelFromRecordUsing(fn (User $record) => $record->full_name ?: $record->username)
                            ->multiple()
                            ->searchable()
                            ->preload(false)
                            ->getSearchResultsUsing(fn (string $search) => self::searchStudents($search))
                            ->getOptionLabelsUsing(fn (array $values) => self::studentLabels($values))
                            ->helperText('Учні, закріплені за групою.'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Назва')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('primaryTeacher.full_name')
                    ->label('Основний викладач')
                    ->formatStateUsing(fn ($state, StudyGroup $record) => $record->primaryTeacher?->full_name ?: $record->primaryTeacher?->username ?: '—'),
                TextColumn::make('teachers_count')
                    ->counts('teachers')
                    ->label('Викладачі'),
                TextColumn::make('students_count')
                    ->counts('students')
                    ->label('Учні'),
                BadgeColumn::make('is_active')
                    ->label('Статус')
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ])
                    ->formatStateUsing(fn ($state) => $state ? 'Активна' : 'Неактивна'),
                TextColumn::make('updated_at')
                    ->label('Оновлено')
                    ->dateTime('d.m.Y H:i'),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Статус')
                    ->options([
                        1 => 'Активні',
                        0 => 'Неактивні',
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
            'index' => Pages\ListStudyGroups::route('/'),
            'create' => Pages\CreateStudyGroup::route('/create'),
            'edit' => Pages\EditStudyGroup::route('/{record}/edit'),
        ];
    }

    protected static function searchTeachers(string $search = ''): array
    {
        return self::searchUsersByRole('teacher', $search);
    }

    protected static function teacherLabels(array $ids): array
    {
        return self::loadUserLabels('teacher', $ids);
    }

    protected static function teacherLabel(int $id): ?string
    {
        return self::teacherLabels([$id])[$id] ?? null;
    }

    protected static function searchStudents(string $search = ''): array
    {
        return self::searchUsersByRole('student', $search);
    }

    protected static function studentLabels(array $ids): array
    {
        return self::loadUserLabels('student', $ids);
    }

    protected static function searchUsersByRole(string $role, string $search = ''): array
    {
        $like = "%{$search}%";

        return User::query()
            ->select(['id', 'first_name', 'last_name', 'username'])
            ->where('role', $role)
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($like) {
                $query->where('username', 'like', $like)
                    ->orWhere('first_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like)
                    ->orWhereRaw("CONCAT_WS(' ', NULLIF(first_name, ''), NULLIF(last_name, '')) LIKE ?", [$like]);
            }))
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->limit(50)
            ->get()
            ->mapWithKeys(fn (User $user) => [$user->id => self::formatUserLabel($user)])
            ->toArray();
    }

    protected static function loadUserLabels(string $role, array $ids): array
    {
        if (! $ids) {
            return [];
        }

        return User::query()
            ->select(['id', 'first_name', 'last_name', 'username'])
            ->where('role', $role)
            ->whereIn('id', $ids)
            ->get()
            ->mapWithKeys(fn (User $user) => [$user->id => self::formatUserLabel($user)])
            ->toArray();
    }

    protected static function formatUserLabel(User $user): string
    {
        $fullName = trim(sprintf('%s %s', $user->first_name ?? '', $user->last_name ?? ''));
        return $fullName !== '' ? $fullName : $user->username;
    }
}
