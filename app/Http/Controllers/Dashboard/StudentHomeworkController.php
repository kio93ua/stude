<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\HomeworkStatus;
use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\TeacherMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StudentHomeworkController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user();

        $homeworks = Homework::query()
            ->with([
                'lesson',
                'teacher',
                'students' => fn ($query) => $query->where('users.id', $student->id),
            ])
            ->whereHas('students', fn ($query) => $query->where('users.id', $student->id))
            ->orderByRaw('COALESCE(due_at, created_at) asc')
            ->paginate(10);

        return view('student.homework.index', [
            'homeworks' => $homeworks,
        ]);
    }

    public function show(Request $request, Homework $homework)
    {
        $student = $request->user();

        $homework->load([
            'lesson',
            'teacher',
            'students' => fn ($query) => $query->where('users.id', $student->id),
            'submissions' => fn ($query) => $query->where('student_id', $student->id),
        ]);

        abort_unless($homework->students->pluck('id')->contains($student->id), 403);

        $pivot = $homework->students->first()?->pivot;
        if ($pivot) {
            $currentStatus = HomeworkStatus::tryFrom($pivot->status ?? '') ?? HomeworkStatus::ASSIGNED;

            if ($currentStatus === HomeworkStatus::ASSIGNED) {
                $homework->students()->updateExistingPivot($student->id, [
                    'status' => HomeworkStatus::VIEWED->value,
                ]);

                $pivot->status = HomeworkStatus::VIEWED->value;
                $pivot->updated_at = now();
            }
        }
        $submission = $homework->submissions->first();

        return view('student.homework.show', [
            'homework' => $homework,
            'pivot' => $pivot,
            'submission' => $submission,
        ]);
    }

    public function submit(Request $request, Homework $homework)
    {
        $student = $request->user();

        abort_unless($homework->students()->where('users.id', $student->id)->exists(), 403);

        $validated = $request->validate([
            'body' => ['nullable', 'string'],
            'images' => ['nullable', 'array', 'max:7'],
            'images.*' => ['image', 'max:3072'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['string'],
        ]);

        $submission = HomeworkSubmission::firstOrNew([
            'homework_id' => $homework->id,
            'student_id' => $student->id,
        ]);

        $existingImages = collect($submission->images ?? []);
        $removeImages = collect($validated['remove_images'] ?? [])
            ->filter(fn ($path) => $existingImages->contains($path));

        $imagesToKeep = $existingImages
            ->reject(fn ($path) => $removeImages->contains($path))
            ->values();

        $newFiles = $request->file('images', []);

        if (blank($validated['body'] ?? null) && $imagesToKeep->isEmpty() && empty($newFiles)) {
            return back()->withErrors(['body' => 'Додайте текст або хоча б одну фотографію.'])->withInput();
        }

        if ($imagesToKeep->count() + count($newFiles) > 7) {
            return back()->withErrors(['images' => 'Можна додати не більше 7 фотографій разом із наявними.'])->withInput();
        }

        $storedImages = collect($newFiles)
            ->map(fn ($file) => $file->store('homework/submissions', 'public'))
            ->all();

        if ($removeImages->isNotEmpty()) {
            Storage::disk('public')->delete($removeImages->all());
        }

        DB::transaction(function () use ($submission, $homework, $student, $validated, $imagesToKeep, $storedImages) {
            $submission->body = $validated['body'] ?? null;
            $submission->images = $imagesToKeep->merge($storedImages)->unique()->values()->all();
            $submission->status = HomeworkStatus::SUBMITTED;
            $submission->teacher_feedback = null;
            $submission->feedback_left_at = null;
            $submission->submitted_at = now();
            $submission->save();

            $homework->students()->updateExistingPivot($student->id, [
                'status' => HomeworkStatus::SUBMITTED->value,
                'submitted_at' => now(),
            ]);

            TeacherMessage::create([
                'sender_id' => $student->id,
                'recipient_id' => $homework->teacher_id,
                'subject' => 'Нове домашнє завдання: ' . $homework->title,
                'body' => 'Учень ' . ($student->username ?? $student->email) . ' надіслав домашнє завдання.' . PHP_EOL . PHP_EOL . ($submission->body ? str($submission->body)->limit(180) : ''),
                'sent_at' => now(),
            ]);
        });

        return back()->with('status', 'Домашнє завдання надіслано викладачу.');
    }
}
