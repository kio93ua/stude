<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\StudyTest;
use App\Models\TestAnswer;
use App\Models\TestAttempt;
use App\Models\TestResult;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentTestController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user();

        $tests = StudyTest::query()
            ->withCount('questions')
            ->where(function ($query) {
                $query->whereNull('available_from')
                    ->orWhere('available_from', '<=', now());
            })
            ->with(['lesson', 'teacher'])
            ->orderByRaw('COALESCE(due_at, "9999-12-31")')
            ->get();

        $attempts = $student->testAttempts()
            ->latest('updated_at')
            ->get()
            ->keyBy('test_id');

        return view('student.tests.index', [
            'tests' => $tests,
            'attempts' => $attempts,
        ]);
    }

    public function show(Request $request, StudyTest $test): View
    {
        $student = $request->user();
        $this->authorizeForStudent($student->id, $test);

        $test->load(['questions.options' => fn ($query) => $query->orderBy('position')]);
        $existingAttempt = TestAttempt::query()
            ->where('test_id', $test->id)
            ->where('student_id', $student->id)
            ->orderByDesc('created_at')
            ->first();

        $isLocked = $existingAttempt && $existingAttempt->status === 'submitted';

        $attempt = $isLocked
            ? $existingAttempt
            : TestAttempt::firstOrCreate(
                [
                    'test_id' => $test->id,
                    'student_id' => $student->id,
                    'status' => 'in_progress',
                ],
                [
                    'max_score' => $test->questions->sum('points'),
                    'started_at' => now(),
                ],
            );

        return view('student.tests.show', [
            'test' => $test,
            'attempt' => $attempt,
            'isLocked' => $isLocked,
        ]);
    }

    public function submit(Request $request, StudyTest $test): RedirectResponse
    {
        $student = $request->user();
        $this->authorizeForStudent($student->id, $test);

        $test->load(['questions.options' => fn ($query) => $query->orderBy('position')]);
        $attempt = TestAttempt::where('test_id', $test->id)
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->latest('started_at')
            ->firstOrFail();

        $payload = $request->input('answers', []);

        DB::transaction(function () use ($payload, $test, $attempt, $student) {
            $answersCollection = collect($payload);

            $attempt->answers()->delete();

            $score = 0;
            $maxScore = $test->questions->sum('points');

            foreach ($test->questions as $question) {
                $answerData = $answersCollection->get((string) $question->id);
                $selectedOptionIds = null;
                $textAnswer = null;
                $isCorrect = null;
                $awardedPoints = 0;

                if ($question->type === 'text') {
                    $textAnswer = trim((string) ($answerData ?? ''));
                    $isCorrect = null; // потребує ручної перевірки
                } else {
                    $selected = Arr::wrap($answerData);
                    $selectedOptionIds = array_filter(array_map('intval', $selected));

                    $correctOptionIds = $question->options
                        ->where('is_correct', true)
                        ->pluck('id')
                        ->map(fn ($id) => (int) $id)
                        ->sort()
                        ->values()
                        ->all();

                    $provided = collect($selectedOptionIds)->sort()->values()->all();

                    if ($question->type === 'single_choice') {
                        $isCorrect = count($correctOptionIds) === 1
                            && $provided === $correctOptionIds;
                    } else { // multiple_choice
                        $isCorrect = $provided === $correctOptionIds && count($correctOptionIds) > 0;
                    }

                    if ($isCorrect) {
                        $awardedPoints = $question->points;
                        $score += $question->points;
                    }
                }

                TestAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'selected_option_ids' => $selectedOptionIds,
                    'text_answer' => $textAnswer,
                    'is_correct' => $isCorrect,
                    'awarded_points' => $awardedPoints,
                ]);
            }

            $attempt->update([
                'status' => 'submitted',
                'submitted_at' => now(),
                'score' => $score,
                'max_score' => $maxScore,
            ]);

            $result = TestResult::updateOrCreate(
                [
                    'test_id' => $test->id,
                    'student_id' => $student->id,
                ],
                [
                    'score' => $score,
                    'status' => 'completed',
                    'completed_at' => now(),
                ],
            );

            $attempt->result()->associate($result)->save();
        });

        return redirect()
            ->route('dashboard.student.tests.index')
            ->with('status', 'Результат збережено. Набрано балів: ' . $attempt->fresh()->score);
    }

    private function authorizeForStudent(int $studentId, StudyTest $test): void
    {
        // Тимчасово дозволяємо студентам доступ до всіх тестів незалежно від прив'язки до уроків.
    }
}
