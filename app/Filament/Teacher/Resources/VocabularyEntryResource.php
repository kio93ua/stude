<?php

namespace App\Filament\Teacher\Resources;

use App\Filament\Teacher\Resources\VocabularyEntryResource\Pages;
use App\Models\VocabularyEntry;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class VocabularyEntryResource extends Resource
{
    protected static ?string $model = VocabularyEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationLabel = 'Словник учнів';

    protected static ?string $pluralModelLabel = 'Словник учнів';

    protected static ?string $modelLabel = 'Запис словника';

    protected static ?string $navigationGroup = 'Учні';

    protected static ?int $navigationSort = 25;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('term')
                    ->label('Слово')
                    ->required()
                    ->maxLength(120),
                Forms\Components\TextInput::make('translation')
                    ->label('Переклад')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('definition')
                    ->label('Визначення')
                    ->rows(3),
                Forms\Components\Textarea::make('example')
                    ->label('Приклад')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('term')
                    ->label('Слово')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('translation')
                    ->label('Переклад')
                    ->searchable(),
                TextColumn::make('student.username')
                    ->label('Учень (логін)')
                    ->sortable()
                    ->searchable()
                    ->description(fn (?VocabularyEntry $record) => ($record?->student && $record->student->full_name && $record->student->full_name !== $record->student->username) ? $record->student->full_name : null),
                TextColumn::make('definition')
                    ->label('Визначення')
                    ->limit(80)
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('example')
                    ->label('Приклад')
                    ->limit(80)
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Оновлено')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('student_id')
                    ->label('Учень')
                    ->options(fn () => static::studentOptions())
                    ->searchable(),
            ])
            ->actions([
                EditAction::make()
                    ->label('Редагувати')
                    ->icon('heroicon-o-pencil-square')
                    ->modalHeading(fn (VocabularyEntry $record): string => 'Редагувати: ' . $record->term)
                    ->modalWidth('lg')
                    ->fillForm(fn (VocabularyEntry $record): array => [
                        'term' => $record->term,
                        'translation' => $record->translation,
                        'definition' => $record->definition,
                        'example' => $record->example,
                    ])
                    ->form([
                        Forms\Components\TextInput::make('term')
                            ->label('Слово')
                            ->required()
                            ->maxLength(120),
                        Forms\Components\TextInput::make('translation')
                            ->label('Переклад')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('definition')
                            ->label('Визначення')
                            ->rows(3),
                        Forms\Components\Textarea::make('example')
                            ->label('Приклад')
                            ->rows(3),
                    ])
                    ->action(function (VocabularyEntry $record, array $data): void {
                        $record->fill($data);
                        $record->last_updated_by = Auth::id();
                        $record->save();
                    }),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVocabularyEntries::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return $record !== null;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $studentIds = static::teacherStudentIds();

        return parent::getEloquentQuery()
            ->when(! empty($studentIds), fn (Builder $query) => $query->whereIn('student_id', $studentIds))
            ->with(['student']);
    }

    protected static function teacherStudentIds(): array
    {
        $teacher = Auth::user();

        if (! $teacher || ! method_exists($teacher, 'taughtLessons')) {
            return [];
        }

        $students = $teacher->taughtLessons()
            ->with('students:id,username,name')
            ->get()
            ->flatMap(fn ($lesson) => $lesson->students)
            ->filter()
            ->unique('id');

        if ($students->isEmpty()) {
            return [];
        }

        return $students->pluck('id')->values()->all();
    }

    protected static function studentOptions(): array
    {
        $teacher = Auth::user();

        $students = collect();

        if ($teacher && method_exists($teacher, 'taughtLessons')) {
            $students = $teacher->taughtLessons()
                ->with('students:id,username,name')
                ->get()
                ->flatMap(fn ($lesson) => $lesson->students)
                ->filter()
                ->unique('id');
        }

        if ($students->isEmpty()) {
            $students = static::getModel()::query()
                ->with('student')
                ->get()
                ->map(fn ($entry) => $entry->student)
                ->filter()
                ->unique('id');
        }

        return $students
            ->sortBy('username')
            ->mapWithKeys(fn ($student) => [$student->id => $student->username])
            ->toArray();
    }
}
