<?php

namespace App\Filament\Teacher\Resources;

use App\Enums\HomeworkStatus;
use App\Filament\Teacher\Resources\HomeworkSubmissionResource\Pages;
use App\Models\HomeworkSubmission;
use App\Models\Lesson;
use App\Models\TeacherMessage;
use App\Models\User;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class HomeworkSubmissionResource extends Resource
{
    protected static ?string $model = HomeworkSubmission::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationLabel = 'Відповіді учнів';

    protected static ?string $pluralModelLabel = 'Відповіді учнів';

    protected static ?string $modelLabel = 'Відповідь';

    protected static ?string $navigationGroup = 'Домашні завдання';

    protected static ?int $navigationSort = 15;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('submitted_at')
                    ->label('Надіслано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn ($state): string => ($state instanceof HomeworkStatus ? $state : HomeworkStatus::tryFrom((string) $state))?->label() ?? '—')
                    ->badge()
                    ->color(fn ($state): string => ($state instanceof HomeworkStatus ? $state : HomeworkStatus::tryFrom((string) $state))?->badgeColor() ?? 'gray')
                    ->sortable(),
                TextColumn::make('student.username')
                    ->label('Учень')
                    ->sortable()
                    ->searchable()
                    ->description(fn (HomeworkSubmission $record) => $record->student?->full_name),
                TextColumn::make('homework.title')
                    ->label('Домашнє')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('homework.lesson.title')
                    ->label('Урок')
                    ->sortable()
                    ->wrap(),
                TextColumn::make('body')
                    ->label('Відповідь')
                    ->limit(80)
                    ->wrap()
                    ->toggleable(),
                TextColumn::make('images')
                    ->label('Фото')
                    ->formatStateUsing(fn (HomeworkSubmission $record): string => (string) (is_array($record->images) ? count($record->images) : 0))
                    ->badge()
                    ->color(fn (HomeworkSubmission $record) => (is_array($record->images) && count($record->images)) ? 'success' : 'gray'),
                TextColumn::make('attachments')
                    ->label('Файли')
                    ->formatStateUsing(fn (HomeworkSubmission $record): string => (string) (is_array($record->attachments) ? count($record->attachments) : 0))
                    ->badge()
                    ->color(fn (HomeworkSubmission $record) => (is_array($record->attachments) && count($record->attachments)) ? 'success' : 'gray'),
                TextColumn::make('teacher_feedback')
                    ->label('Відгук викладача')
                    ->limit(80)
                    ->wrap()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('lesson_id')
                    ->label('Урок')
                    ->options(fn () => Lesson::query()
                        ->where('teacher_id', Auth::id() ?? 0)
                        ->orderBy('title')
                        ->pluck('title', 'id')
                        ->toArray())
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;

                        if (blank($value)) {
                            return;
                        }

                        $query->whereHas('homework', fn (Builder $subQuery) => $subQuery->where('lesson_id', $value));
                    }),
                SelectFilter::make('homework_id')
                    ->label('Домашнє')
                    ->relationship('homework', 'title', fn (Builder $query) => $query->where('teacher_id', Auth::id() ?? 0))
                    ->searchable(),
                SelectFilter::make('student_id')
                    ->label('Учень')
                    ->relationship('student', 'username')
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(fn (User $record): string => trim($record->full_name . ' · ' . $record->username)),
            ])
            ->actions([
                Action::make('review')
                    ->label('Оцінити')
                    ->icon('heroicon-o-check-badge')
                    ->modalWidth('lg')
                    ->modalHeading(fn (HomeworkSubmission $record): string => 'Перевірка: ' . ($record->student?->username ?? 'Учень'))
                    ->form([
                        Forms\Components\Select::make('status')
                            ->label('Статус')
                            ->options(HomeworkStatus::options())
                            ->required(),
                        Forms\Components\Textarea::make('teacher_feedback')
                            ->label('Відгук для учня')
                            ->rows(5)
                            ->maxLength(2000),
                        Forms\Components\Toggle::make('notify_student')
                            ->label('Надіслати учню повідомлення у кабінет')
                            ->default(true),
                    ])
                    ->fillForm(fn (HomeworkSubmission $record): array => [
                        'status' => $record->status?->value ?? HomeworkStatus::SUBMITTED->value,
                        'teacher_feedback' => $record->teacher_feedback,
                        'notify_student' => true,
                    ])
                    ->action(function (HomeworkSubmission $record, array $data): void {
                        if (! Schema::hasColumns('homework_submissions', ['status', 'teacher_feedback', 'feedback_left_at'])) {
                            Notification::make()
                                ->danger()
                                ->title('Потрібно оновити базу')
                                ->body('Запустіть міграції (`php artisan migrate`), щоб додати колонки status, teacher_feedback та feedback_left_at до таблиці homework_submissions.')
                                ->send();

                            return;
                        }

                        $status = HomeworkStatus::tryFrom($data['status'] ?? '') ?? HomeworkStatus::SUBMITTED;

                        $previousStatus = $record->status;
                        $previousFeedback = $record->teacher_feedback;

                        $record->status = $status;
                        $feedback = blank($data['teacher_feedback'] ?? null) ? null : trim($data['teacher_feedback']);
                        $record->teacher_feedback = $feedback;
                        $record->feedback_left_at = now();
                        $record->save();

                        if ($record->homework) {
                            $record->homework->students()->updateExistingPivot($record->student_id, [
                                'status' => $status->value,
                            ]);
                        }

                        $feedbackChanged = trim((string) ($previousFeedback ?? '')) !== trim((string) ($feedback ?? ''));
                        $statusChanged = ($previousStatus?->value ?? null) !== $status->value;

                        if (! empty($data['notify_student']) && ($feedbackChanged || $statusChanged) && $record->student) {
                            $teacherId = Auth::id() ?? $record->homework?->teacher_id;

                            if ($teacherId) {
                                $lines = [
                                    'Статус: ' . $status->label(),
                                ];

                                if ($feedback) {
                                    $lines[] = $feedback;
                                }

                                TeacherMessage::create([
                                    'sender_id' => $teacherId,
                                    'recipient_id' => $record->student_id,
                                    'subject' => 'Відгук на домашнє: ' . ($record->homework?->title ?? 'Домашнє завдання'),
                                    'body' => implode(PHP_EOL . PHP_EOL, $lines),
                                    'sent_at' => now(),
                                ]);
                            }
                        }
                    })
                    ->visible(fn (HomeworkSubmission $record): bool => (int) ($record->homework?->teacher_id ?? 0) === (int) (Auth::id() ?? 0)),
                Action::make('view')
                    ->label('Переглянути')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn (HomeworkSubmission $record) => 'Відповідь: ' . ($record->student?->username ?? 'Учень'))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Закрити')
                    ->modalWidth('5xl')
                    ->modalContent(fn (HomeworkSubmission $record) => view('filament.teacher.homework-submission-summary', ['submission' => $record])),
            ])
            ->defaultSort('submitted_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomeworkSubmissions::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('homework', fn (Builder $query) => $query->where('teacher_id', Auth::id()))
            ->with(['homework.lesson', 'student']);
    }
}
