<?php

namespace App\Services\Scheduling;

use App\Models\Lesson;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ConflictChecker
{
    public function findConflicts(int $teacherId, array $studentIds, Carbon $startsAt, Carbon $endsAt, ?int $ignoreLessonId = null): array
    {
        $teacherConflicts = $this->findTeacherConflicts($teacherId, $startsAt, $endsAt, $ignoreLessonId);

        $studentConflicts = collect($studentIds)
            ->unique()
            ->filter()
            ->mapWithKeys(function ($studentId) use ($startsAt, $endsAt, $ignoreLessonId) {
                $conflicts = $this->findStudentConflicts($studentId, $startsAt, $endsAt, $ignoreLessonId);

                return [$studentId => $conflicts];
            })
            ->filter(fn (Collection $conflicts) => $conflicts->isNotEmpty());

        return [
            'teacher' => $teacherConflicts,
            'students' => $studentConflicts,
        ];
    }

    public function hasConflicts(int $teacherId, array $studentIds, Carbon $startsAt, Carbon $endsAt, ?int $ignoreLessonId = null): bool
    {
        $results = $this->findConflicts($teacherId, $studentIds, $startsAt, $endsAt, $ignoreLessonId);

        if ($results['teacher']->isNotEmpty()) {
            return true;
        }

        return $results['students']->isNotEmpty();
    }

    protected function findTeacherConflicts(int $teacherId, Carbon $startsAt, Carbon $endsAt, ?int $ignoreLessonId = null): Collection
    {
        return Lesson::query()
            ->with(['teacher', 'students'])
            ->where('teacher_id', $teacherId)
            ->when($ignoreLessonId, fn ($query) => $query->whereKeyNot($ignoreLessonId))
            ->whereNotNull('starts_at')
            ->where(function ($query) use ($startsAt, $endsAt) {
                $query
                    ->where('starts_at', '<', $endsAt)
                    ->whereRaw('DATE_ADD(starts_at, INTERVAL duration_minutes MINUTE) > ?', [$startsAt->toDateTimeString()]);
            })
            ->orderBy('starts_at')
            ->get();
    }

    protected function findStudentConflicts(int $studentId, Carbon $startsAt, Carbon $endsAt, ?int $ignoreLessonId = null): Collection
    {
        return Lesson::query()
            ->with(['teacher', 'students'])
            ->whereNotNull('starts_at')
            ->when($ignoreLessonId, fn ($query) => $query->whereKeyNot($ignoreLessonId))
            ->whereHas('students', function ($query) use ($studentId) {
                $query->where('users.id', $studentId);
            })
            ->where(function ($query) use ($startsAt, $endsAt) {
                $query
                    ->where('starts_at', '<', $endsAt)
                    ->whereRaw('DATE_ADD(starts_at, INTERVAL duration_minutes MINUTE) > ?', [$startsAt->toDateTimeString()]);
            })
            ->orderBy('starts_at')
            ->get();
    }
}
