<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;

class StudentMaterialController extends Controller
{
    public function index(Request $request)
    {
        $lessons = Lesson::query()
            ->with(['teacher', 'materials' => fn ($query) => $query->latest()])
            ->latest('created_at')
            ->paginate(12);

        return view('student.materials.index', [
            'lessons' => $lessons,
        ]);
    }

    public function show(Request $request, Lesson $lesson)
    {
        $lesson->load(['teacher', 'materials' => fn ($query) => $query->latest()]);

        return view('student.materials.show', [
            'lesson' => $lesson,
        ]);
    }
}
