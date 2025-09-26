<?php

namespace App\Filament\Admin\Resources\LessonResource\Pages;

use App\Filament\Admin\Resources\LessonResource;
use App\Models\Lesson;
use App\Models\LessonSeries;
use App\Models\StudyGroup;
use App\Models\User;
use App\Services\Scheduling\ConflictChecker;
use App\Services\Scheduling\RecurringLessonGenerator;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class CreateLesson extends CreateRecord
{
    protected static string $resource = LessonResource::class;

    protected function handleRecordCreation(array $data): Lesson
    {
        $format = $data['format'] ?? 'individual';
        $groupId = $data['group_id'] ?? null;
        $studentIds = collect($data['student_ids'] ?? [])->filter()->map(fn ($id) => (int) $id)->values()->all();
        $createSeries = (bool) ($data['create_series'] ?? false);

        if ($format === 'group') {
            if (! $groupId) {
                Notification::make()
                    ->title('Оберіть групу')
                    ->body('Для групового уроку потрібно вказати навчальну групу.')
                    ->danger()
                    ->send();

                $this->halt();
            }

            $studentIds = $this->resolveGroupStudentIds((int) $groupId);

            if ($studentIds === []) {
                Notification::make()
                    ->title('У групі немає студентів')
                    ->body('Додайте учнів до групи перед створенням уроку.')
                    ->danger()
                    ->send();

                $this->halt();
            }
        }

        $seriesWeekday = $data['series_weekday'] ?? null;
        $seriesStartsOn = $data['series_starts_on'] ?? null;
        $seriesEndsOn = $data['series_ends_on'] ?? null;
        $seriesTime = $data['series_time'] ?? null;

        Arr::forget($data, ['student_ids', 'create_series', 'series_weekday', 'series_starts_on', 'series_ends_on', 'series_time']);

        $duration = (int) ($data['duration_minutes'] ?? 60);
        $data['duration_minutes'] = $duration;
        $data['timezone'] = config('app.timezone');

        if ($createSeries) {
            return $this->createSeries($data, $studentIds, (int) $seriesWeekday, $seriesStartsOn, $seriesEndsOn, $seriesTime, $duration);
        }

        return $this->createSingleLesson($data, $studentIds, $duration);
    }

    protected function createSeries(array $data, array $studentIds, int $weekday, ?string $startsOn, ?string $endsOn, ?string $time, int $duration): Lesson
    {
        if (! $startsOn || ! $time) {
            Notification::make()
                ->title('Недостатньо даних для серії')
                ->body('Вкажіть дату початку та час уроку.')
                ->danger()
                ->send();

            $this->halt();
        }

        $series = LessonSeries::create([
            'teacher_id' => $data['teacher_id'],
            'primary_student_id' => $studentIds[0] ?? null,
            'group_id' => $data['group_id'] ?? null,
            'format' => $data['format'] ?? 'individual',
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'weekday' => $weekday,
            'starts_at' => $this->normalizeTime($time),
            'duration_minutes' => $duration,
            'starts_on' => $startsOn,
            'ends_on' => $endsOn,
            'is_active' => true,
        ]);

        /** @var RecurringLessonGenerator $generator */
        $generator = app(RecurringLessonGenerator::class);
        $generator->ensureOccurrences($series, $studentIds);

        $series->refresh();

        if ($series->lessons()->count() === 0) {
            $series->delete();

            Notification::make()
                ->title('Не вдалося створити серію')
                ->body('Не знайдено вільних слотів для вказаного розкладу.')
                ->danger()
                ->send();

            $this->halt();
        }

        $lesson = $series->lessons()->orderBy('scheduled_for')->firstOrFail();

        Notification::make()
            ->title('Серію створено')
            ->body('Перший урок додано до розкладу. Інші заняття згенеровано автоматично.')
            ->success()
            ->send();

        return $lesson;
    }

    protected function createSingleLesson(array $data, array $studentIds, int $duration): Lesson
    {
        if (empty($data['starts_at'])) {
            Notification::make()
                ->title('Вкажіть дату та час')
                ->danger()
                ->send();

            $this->halt();
        }

        $startsAt = Carbon::parse($data['starts_at']);
        $endsAt = $startsAt->copy()->addMinutes($duration);
        $data['starts_at'] = $startsAt;
        $data['scheduled_for'] = $startsAt->toDateString();

        /** @var ConflictChecker $conflictChecker */
        $conflictChecker = app(ConflictChecker::class);
        $conflicts = $conflictChecker->findConflicts($data['teacher_id'], $studentIds, $startsAt, $endsAt);

        if ($conflicts['teacher']->isNotEmpty() || $conflicts['students']->isNotEmpty()) {
            Notification::make()
                ->title('Конфлікт у розкладі')
                ->body($this->formatConflicts($conflicts))
                ->danger()
                ->send();

            $this->halt();
        }

        /** @var Lesson $lesson */
        $lesson = static::getModel()::create($data);

        if ($studentIds !== []) {
            $lesson->students()->sync($studentIds);
        }

        return $lesson;
    }

    protected function normalizeTime(string $time): string
    {
        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $time)) {
            return $time;
        }

        return Carbon::parse($time)->format('H:i:s');
    }

    protected function formatConflicts(array $conflicts): string
    {
        $parts = [];

        if ($conflicts['teacher']->isNotEmpty()) {
            $teacherConflicts = $conflicts['teacher']
                ->map(fn (Lesson $lesson) => $lesson->starts_at?->format('d.m H:i') . ' · ' . $lesson->title)
                ->join(', ');

            $parts[] = 'Викладач зайнятий: ' . $teacherConflicts;
        }

        if ($conflicts['students']->isNotEmpty()) {
            $studentIds = $conflicts['students']->keys();
            $students = User::query()->whereIn('id', $studentIds)->get()->keyBy('id');

            foreach ($conflicts['students'] as $studentId => $lessons) {
                $student = $students->get($studentId);
                $name = $student?->full_name ?: $student?->username ?: ('ID ' . $studentId);

                $lessonsList = $lessons
                    ->map(fn (Lesson $lesson) => $lesson->starts_at?->format('d.m H:i') . ' · ' . $lesson->title)
                    ->join(', ');

                $parts[] = $name . ' зайнятий: ' . $lessonsList;
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
