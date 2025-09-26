<?php

namespace App\Filament\Admin\Resources\LessonResource\Pages;

use App\Filament\Admin\Resources\LessonResource;
use App\Models\Lesson;
use App\Models\StudyGroup;
use App\Models\User;
use App\Services\Scheduling\ConflictChecker;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class EditLesson extends EditRecord
{
    protected static string $resource = LessonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['student_ids'] = $this->record->students->pluck('id')->all();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var Lesson $lesson */
        $lesson = $record;

        $format = $data['format'] ?? $lesson->format ?? 'individual';
        $groupId = $data['group_id'] ?? $lesson->group_id;

        $studentIds = collect($data['student_ids'] ?? [])->filter()->map(fn ($id) => (int) $id)->values()->all();

        if ($format === 'group') {
            if (! $groupId) {
                Notification::make()
                    ->title('Група не вибрана')
                    ->body('Для групового уроку потрібно обрати навчальну групу.')
                    ->danger()
                    ->send();

                $this->halt();
            }

            $groupId = (int) $groupId;
            $studentIds = $this->resolveGroupStudentIds($groupId);

            if ($studentIds === []) {
                Notification::make()
                    ->title('У групі немає студентів')
                    ->body('Додайте учнів до групи перед збереженням уроку.')
                    ->danger()
                    ->send();

                $this->halt();
            }

            $data['group_id'] = $groupId;
        }

        Arr::forget($data, ['student_ids', 'create_series', 'series_weekday', 'series_starts_on', 'series_ends_on', 'series_time']);

        $duration = (int) ($data['duration_minutes'] ?? $lesson->duration_minutes ?? 60);
        $data['duration_minutes'] = $duration;
        $data['timezone'] = $lesson->timezone ?? config('app.timezone');

        if (! empty($data['starts_at'])) {
            $startsAt = Carbon::parse($data['starts_at']);
            $endsAt = $startsAt->copy()->addMinutes($duration);
            $data['scheduled_for'] = $startsAt->toDateString();
        } else {
            $startsAt = $lesson->starts_at ?? Carbon::parse($lesson->scheduled_for . ' ' . ($lesson->starts_at?->format('H:i') ?? '00:00'));
            $endsAt = $startsAt->copy()->addMinutes($duration);
        }

        /** @var ConflictChecker $conflictChecker */
        $conflictChecker = app(ConflictChecker::class);
        $conflicts = $conflictChecker->findConflicts($data['teacher_id'] ?? $lesson->teacher_id, $studentIds, $startsAt, $endsAt, $lesson->id);

        if ($conflicts['teacher']->isNotEmpty() || $conflicts['students']->isNotEmpty()) {
            Notification::make()
                ->title('Конфлікт у розкладі')
                ->body($this->formatConflicts($conflicts))
                ->danger()
                ->send();

            $this->halt();
        }

        $lesson->update($data);

        $lesson->students()->sync($studentIds);

        return $lesson;
    }

    protected function formatConflicts(array $conflicts): string
    {
        $parts = [];

        if ($conflicts['teacher']->isNotEmpty()) {
            $parts[] = 'Викладач зайнятий: ' . $conflicts['teacher']
                ->map(fn (Lesson $lesson) => $lesson->starts_at?->format('d.m H:i') . ' · ' . $lesson->title)
                ->join(', ');
        }

        if ($conflicts['students']->isNotEmpty()) {
            $studentIds = $conflicts['students']->keys();
            $students = User::query()->whereIn('id', $studentIds)->get()->keyBy('id');

            foreach ($conflicts['students'] as $studentId => $lessons) {
                $student = $students->get($studentId);
                $name = $student?->full_name ?: $student?->username ?: ('ID ' . $studentId);

                $parts[] = $name . ' зайнятий: ' . $lessons
                    ->map(fn (Lesson $lesson) => $lesson->starts_at?->format('d.m H:i') . ' · ' . $lesson->title)
                    ->join(', ');
            }
        }

        return implode("\n", $parts);
    }

    protected function resolveGroupStudentIds(int $groupId): array
    {
        /** @var StudyGroup|null $group */
        $group = StudyGroup::query()->with('students:id')->find($groupId);

        if (! $group) {
            return [];
        }

        return $group->students->pluck('id')->map(fn ($id) => (int) $id)->all();
    }
}
