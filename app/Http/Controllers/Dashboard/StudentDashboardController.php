<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\Lesson;
use App\Models\LessonMaterial;
use App\Models\StudyTest;
use App\Models\TeacherMessage;
use App\Models\TestResult;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class StudentDashboardController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user();

        if (! $student || ! $student->isStudent()) {
            $student = User::where('role', 'student')->first();
        }

        if (! $student) {
            return view('dashboards.student')->with([
                'student' => null,
                'enrollments' => collect(),
                'materials' => collect(),
                'homeworks' => collect(),
                'tests' => collect(),
                'results' => collect(),
                'messages' => collect(),
                'availableLessons' => collect(),
                'progressMetrics' => [],
            ]);
        }

        $enrollments = $student->lessonEnrollments()
            ->with(['lesson' => fn ($query) => $query->with('teacher')])
            ->leftJoin('lessons', 'lesson_enrollments.lesson_id', '=', 'lessons.id')
            ->orderByRaw('COALESCE(lessons.starts_at, lesson_enrollments.created_at) asc')
            ->select('lesson_enrollments.*')
            ->take(6)
            ->get();

        $lessonIds = $enrollments->pluck('lesson.id')->filter()->unique();

        $materials = LessonMaterial::query()
            ->with('lesson')
            ->latest()
            ->take(8)
            ->get();

        $homeworks = Homework::query()
            ->with(['lesson', 'teacher', 'students' => fn ($query) => $query->where('users.id', $student->id)])
            ->whereHas('students', fn ($query) => $query->where('users.id', $student->id))
            ->orderByRaw('COALESCE(due_at, created_at) asc')
            ->take(6)
            ->get();

        $tests = StudyTest::query()
            ->where(function ($query) {
                $query->whereNull('available_from')
                    ->orWhere('available_from', '<=', now());
            })
            ->orderByRaw('COALESCE(due_at, "9999-12-31")')
            ->take(6)
            ->get();

        $results = TestResult::query()
            ->with('test')
            ->where('student_id', $student->id)
            ->latest('completed_at')
            ->take(6)
            ->get();

        $messages = TeacherMessage::query()
            ->with('sender')
            ->where('recipient_id', $student->id)
            ->latest('sent_at')
            ->take(5)
            ->get();

        $availableLessons = Lesson::query()
            ->with('teacher')
            ->latest('created_at')
            ->take(6)
            ->get();

        $progressMetrics = $this->buildProgressMetrics($enrollments, $results);

        return view('dashboards.student', [
            'student' => $student,
            'enrollments' => $enrollments,
            'materials' => $materials,
            'homeworks' => $homeworks,
            'tests' => $tests,
            'results' => $results,
            'messages' => $messages,
            'availableLessons' => $availableLessons,
            'progressMetrics' => $progressMetrics,
        ]);
    }

    protected function buildProgressMetrics($enrollments, Collection $results): array
    {
        $overall = (int) round($enrollments->avg('progress_percent') ?? 0);

        $categoryScores = $results
            ->groupBy(fn ($result) => optional($result->test)->category ?? 'general')
            ->map(fn (Collection $items) => (int) round($items->avg('score') ?? 0));

        return [
            'overall' => $overall,
            'categories' => $categoryScores,
            'lessonsTotal' => $enrollments->count(),
            'lessonsCompleted' => $enrollments->where('status', 'completed')->count(),
        ];
    }
}
