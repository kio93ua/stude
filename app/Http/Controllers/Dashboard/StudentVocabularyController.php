<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\VocabularyEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentVocabularyController extends Controller
{
    public function index(Request $request): View
    {
        $student = $request->user();

        $search = trim((string) $request->query('q', ''));

        $entries = $student->vocabularyEntries()
            ->with('lastUpdatedBy')
            ->when($search, function ($query) use ($search) {
                $query->where(fn ($subQuery) => $subQuery
                    ->where('term', 'like', "%{$search}%")
                    ->orWhere('translation', 'like', "%{$search}%")
                    ->orWhere('definition', 'like', "%{$search}%"));
            })
            ->orderBy('term')
            ->paginate(15)
            ->withQueryString();

        return view('student.vocabulary.index', [
            'entries' => $entries,
            'student' => $student,
            'search' => $search,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $student = $request->user();

        $data = $this->validateData($request, $student->id);

        $student->vocabularyEntries()->create($data + [
            'last_updated_by' => $student->id,
        ]);

        return redirect()
            ->route('dashboard.student.vocabulary.index')
            ->with('status', 'Слово додано до словника.');
    }

    public function update(Request $request, VocabularyEntry $entry): RedirectResponse
    {
        $student = $request->user();

        abort_unless($entry->student_id === $student->id, 403);

        $data = $this->validateData($request, $student->id, $entry->id);

        $entry->fill($data);
        $entry->last_updated_by = $student->id;
        $entry->save();

        return redirect()
            ->route('dashboard.student.vocabulary.index')
            ->with('status', 'Запис словника оновлено.');
    }

    public function destroy(Request $request, VocabularyEntry $entry): RedirectResponse
    {
        $student = $request->user();

        abort_unless($entry->student_id === $student->id, 403);

        $entry->delete();

        return redirect()
            ->route('dashboard.student.vocabulary.index')
            ->with('status', 'Запис словника видалено.');
    }

    private function validateData(Request $request, int $studentId, ?int $entryId = null): array
    {
        $termRule = Rule::unique('vocabulary_entries', 'term')
            ->where(fn ($query) => $query->where('student_id', $studentId));

        if ($entryId) {
            $termRule->ignore($entryId);
        }

        return $request->validate([
            'term' => ['required', 'string', 'max:120', $termRule],
            'translation' => ['required', 'string', 'max:255'],
            'definition' => ['nullable', 'string'],
            'example' => ['nullable', 'string'],
        ]);
    }
}
