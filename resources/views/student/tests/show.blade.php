@extends('layouts.dashboard')

@section('title', 'Проходження тесту')
@section('panel-label', 'Учень')
@section('heading', $test->title)
@section('subheading', 'Виконайте тест і надішліть результати')

@section('sidebar')
    <a href="{{ route('dashboard.student') }}" class="flex items-center justify-between rounded-xl px-3 py-2 transition hover:bg-slate-800">
        <span>Головна</span>
        <span class="text-xs text-slate-500">›</span>
    </a>
    <a href="{{ route('dashboard.student.tests.index') }}" class="flex items-center justify-between rounded-xl px-3 py-2 transition hover:bg-slate-800">
        <span>Тести</span>
        <span class="text-xs text-slate-500">›</span>
    </a>
    <a href="{{ route('dashboard.student.homework.index') }}" class="flex items-center justify-between rounded-xl px-3 py-2 transition hover:bg-slate-800">
        <span>Домашні завдання</span>
        <span class="text-xs text-slate-500">›</span>
    </a>
    <a href="{{ route('dashboard.student.materials.index') }}" class="flex items-center justify-between rounded-xl px-3 py-2 transition hover:bg-slate-800">
        <span>Матеріали</span>
        <span class="text-xs text-slate-500">›</span>
    </a>
@endsection

@section('content')
    <div class="mb-8 rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg">
        <p class="text-sm text-slate-300">Викладач: {{ $test->teacher?->full_name ?? '—' }}</p>
        <p class="text-sm text-slate-300">Урок: {{ $test->lesson?->title ?? 'Не прив’язаний' }}</p>
        <p class="text-sm text-slate-300">Макс. балів: {{ $test->questions->sum('points') }}</p>
        @if($test->due_at)
            <p class="text-sm text-rose-300">Дедлайн: {{ $test->due_at->diffForHumans() }}</p>
        @endif
    </div>

    @if (!empty($isLocked) && $isLocked)
        <div class="rounded-3xl border border-emerald-600 bg-emerald-500/10 px-6 py-8 text-sm text-emerald-200">
            Ви вже пройшли цей тест. Результат: {{ $attempt?->score ?? 0 }} / {{ $attempt?->max_score ?? $test->questions->sum('points') }} балів.
        </div>
    @else
        <form method="POST" action="{{ route('dashboard.student.tests.submit', $test) }}" class="space-y-8">
            @csrf
            @foreach ($test->questions as $index => $question)
            <article class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg space-y-4">
                <header class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-white">Питання {{ $index + 1 }} — {{ $question->points }} бал(ів)</h2>
                    @if ($question->category)
                        <span class="rounded-full bg-slate-800 px-3 py-1 text-xs text-slate-300">{{ $question->category }}</span>
                    @endif
                </header>
                <p class="text-sm text-slate-200">{!! nl2br(e($question->prompt)) !!}</p>
                @if ($question->rich_content)
                    <div class="prose prose-invert max-w-none text-sm text-slate-200">{!! $question->rich_content !!}</div>
                @endif
                @if ($question->attachment_path)
                    <div>
                        <img src="{{ Storage::disk('public')->url($question->attachment_path) }}" alt="Прикріплене зображення" class="max-h-64 rounded-2xl border border-slate-800">
                    </div>
                @endif

                <div class="space-y-3 text-sm text-slate-200">
                    @if ($question->type === 'single_choice')
                        @foreach ($question->options as $option)
                            <label class="flex items-start gap-3 rounded-2xl border border-slate-800 bg-slate-900/70 px-4 py-3">
                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" class="mt-1 h-4 w-4 border-slate-500 text-indigo-500">
                                <div>
                                    <p>{{ $option->label }}</p>
                                    @if ($option->attachment_path)
                                        <img src="{{ Storage::disk('public')->url($option->attachment_path) }}" alt="Варіант" class="mt-2 max-h-40 rounded-xl border border-slate-800">
                                    @endif
                                    @if ($option->feedback)
                                        <p class="mt-1 text-xs text-slate-400">{{ $option->feedback }}</p>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    @elseif ($question->type === 'multiple_choice')
                        @foreach ($question->options as $option)
                            <label class="flex items-start gap-3 rounded-2xl border border-slate-800 bg-slate-900/70 px-4 py-3">
                                <input type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $option->id }}" class="mt-1 h-4 w-4 border-slate-500 text-indigo-500">
                                <div>
                                    <p>{{ $option->label }}</p>
                                    @if ($option->attachment_path)
                                        <img src="{{ Storage::disk('public')->url($option->attachment_path) }}" alt="Варіант" class="mt-2 max-h-40 rounded-xl border border-slate-800">
                                    @endif
                                    @if ($option->feedback)
                                        <p class="mt-1 text-xs text-slate-400">{{ $option->feedback }}</p>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                        <p class="text-xs text-slate-500">Можна обирати кілька варіантів.</p>
                    @else
                        <textarea name="answers[{{ $question->id }}]" rows="4" class="w-full rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-slate-200 focus:border-indigo-400 focus:outline-none" placeholder="Ваша відповідь..."></textarea>
                    @endif
                </div>
            </article>
            @endforeach

            <div class="flex justify-end">
                <button type="submit" class=" rounded-full bg-emerald-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-900/30 transition hover:bg-emerald-400">
                    Надіслати відповіді
                </button>
            </div>
        </form>
    @endif
@endsection
