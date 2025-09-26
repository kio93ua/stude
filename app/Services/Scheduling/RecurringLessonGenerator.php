<?php

namespace App\Services\Scheduling;

use App\Models\Lesson;
use App\Models\LessonSeries;
use App\Models\User;
use Illuminate\Support\Carbon;

class RecurringLessonGenerator
{
    public function __construct(private readonly ConflictChecker $conflictChecker)
    {
    }

    public function ensureOccurrences(LessonSeries $series, array $studentIds = [], ?Carbon $generateUntil = null): void
    {
        if (! $series->is_active) {
            return;
        }

        $series->loadMissing('teacher.availabilities', 'teacher.availabilityExceptions', 'group.students');

        /** @var User|null $teacher */
        $teacher = $series->teacher;

        if (! $teacher) {
            return;
        }

        $startDate = Carbon::parse($series->starts_on)->startOfDay();
        $endDate = $this->resolveEndDate($series, $generateUntil);
        $currentDate = $this->moveToWeekday($startDate->copy(), (int) $series->weekday);
        $lessonDuration = (int) $series->duration_minutes;
        $studentIds = $studentIds !== [] ? $studentIds : $this->resolveStudentIds($series);
        $format = $series->format ?? 'individual';

        while ($currentDate->lte($endDate)) {
            $startsAt = $this->combineDateAndTime($currentDate, $series->starts_at);
            $endsAt = $startsAt->copy()->addMinutes($lessonDuration);

            if ($this->isDateAvailable($teacher, $startsAt, $endsAt) &&
                ! $this->conflictChecker->hasConflicts($teacher->id, $studentIds, $startsAt, $endsAt)) {
                $this->createLessonIfMissing($series, $startsAt, $lessonDuration, $studentIds, $format);
            }

            $currentDate->addWeek();
        }
    }

    protected function resolveEndDate(LessonSeries $series, ?Carbon $generateUntil): Carbon
    {
        if ($generateUntil) {
            return $generateUntil->copy();
        }

        if ($series->ends_on) {
            return Carbon::parse($series->ends_on)->endOfDay();
        }

        return Carbon::now()->addMonths(2)->endOfDay();
    }

    protected function moveToWeekday(Carbon $date, int $weekday): Carbon
    {
        $isoWeekday = $weekday + 1;

        if ($date->dayOfWeekIso === $isoWeekday) {
            return $date;
        }

        return $date->next($isoWeekday);
    }

    protected function combineDateAndTime(Carbon $date, mixed $time): Carbon
    {
        if ($time instanceof Carbon) {
            $time = $time->format('H:i:s');
        }

        [$hour, $minute, $second] = array_pad(explode(':', (string) $time), 3, 0);

        return $date->copy()->setTime((int) $hour, (int) $minute, (int) $second);
    }

    protected function isDateAvailable(User $teacher, Carbon $startsAt, Carbon $endsAt): bool
    {
        $exception = $teacher->availabilityExceptions
            ->firstWhere('date', $startsAt->toDateString());

        if ($exception) {
            return $exception->is_available;
        }

        $activeSlots = $teacher->availabilities->where('is_active', true);

        if ($activeSlots->isEmpty()) {
            return true;
        }

        $weekday = ($startsAt->dayOfWeekIso + 6) % 7;

        $availabilities = $activeSlots->where('weekday', $weekday);

        if ($availabilities->isEmpty()) {
            return false;
        }

        foreach ($availabilities as $availability) {
            if ($this->timeRangeFits((string) $availability->starts_at, (string) $availability->ends_at, $startsAt, $endsAt)) {
                return true;
            }
        }

        return false;
    }

    protected function timeRangeFits(mixed $availabilityStart, mixed $availabilityEnd, Carbon $lessonStart, Carbon $lessonEnd): bool
    {
        $startString = $this->normalizeTimeString($availabilityStart);
        $endString = $this->normalizeTimeString($availabilityEnd);

        $availabilityStartTime = Carbon::createFromFormat('H:i:s', $startString)->setDateFrom($lessonStart);
        $availabilityEndTime = Carbon::createFromFormat('H:i:s', $endString)->setDateFrom($lessonStart);

        return $availabilityStartTime->lte($lessonStart) && $availabilityEndTime->gte($lessonEnd);
    }

    protected function normalizeTimeString(mixed $value): string
    {
        if ($value instanceof Carbon) {
            return $value->format('H:i:s');
        }

        $string = (string) $value;

        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $string)) {
            return $string;
        }

        return Carbon::parse($string)->format('H:i:s');
    }

    protected function createLessonIfMissing(LessonSeries $series, Carbon $startsAt, int $durationMinutes, array $studentIds, string $format): void
    {
        $existing = $series->lessons()
            ->whereDate('scheduled_for', $startsAt->toDateString())
            ->first();

        if ($existing) {
            return;
        }

        $lesson = Lesson::create([
            'teacher_id' => $series->teacher_id,
            'series_id' => $series->id,
            'group_id' => $series->group_id,
            'title' => $series->title,
            'description' => $series->description,
            'starts_at' => $startsAt,
            'scheduled_for' => $startsAt->toDateString(),
            'duration_minutes' => $durationMinutes,
            'status' => 'scheduled',
            'format' => $format,
            'is_temporary' => false,
        ]);

        if ($studentIds !== []) {
            $lesson->students()->syncWithoutDetaching($studentIds);
        }
    }

    protected function resolveStudentIds(LessonSeries $series): array
    {
        if ($series->group) {
            return $series->group->students->pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        if (! $series->primary_student_id) {
            return [];
        }

        return [$series->primary_student_id];
    }
}
