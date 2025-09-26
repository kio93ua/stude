<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\StudyTest;
use App\Models\TeacherMessage;
use App\Models\TestResult;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherDashboardController extends Controller
{
    public function index(Request $request)
    {
        $teacher = $request->user();

        if (! $teacher || ! $teacher->isTeacher()) {
            $teacher = User::where('role', 'teacher')->first();
        }

        if (! $teacher) {
            return view('dashboards.teacher')->with([
                'teacher' => null,
                'lessons' => collect(),
                'tests' => collect(),
                'pendingHomework' => collect(),
                'messages' => collect(),
            ]);
        }

        $lessons = Lesson::query()
            ->where('teacher_id', $teacher->id)
            ->orderByRaw('COALESCE(starts_at, created_at) asc')
            ->take(6)
            ->get();

        $tests = StudyTest::query()
            ->where('teacher_id', $teacher->id)
            ->latest('due_at')
            ->take(6)
            ->get();

        $pendingHomework = TestResult::query()
            ->with(['student', 'test'])
            ->where('status', 'pending')
            ->whereHas('test', fn ($query) => $query->where('teacher_id', $teacher->id))
            ->latest()
            ->take(8)
            ->get();

        $messages = TeacherMessage::query()
            ->with(['recipient'])
            ->where('sender_id', $teacher->id)
            ->latest('sent_at')
            ->take(5)
            ->get();

        return view('dashboards.teacher', [
            'teacher' => $teacher,
            'lessons' => $lessons,
            'tests' => $tests,
            'pendingHomework' => $pendingHomework,
            'messages' => $messages,
        ]);
    }
}
