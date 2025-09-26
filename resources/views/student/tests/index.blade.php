@extends('layouts.dashboard')

@section('title', 'Тести')
@section('panel-label', 'Учень')
@section('heading', 'Мої тести')
@section('subheading', 'Список доступних завдань та їх статус')

@section('sidebar')
    <a href="{{ route('dashboard.student') }}" class="flex items-center justify-between rounded-xl px-3 py-2 transition hover:bg-slate-800">
        <span>Головна</span>
        <span class="text-xs text-slate-500">›</span>
    </a>
    <a href="{{ route('dashboard.student.tests.index') }}" class="flex items-center justify-between rounded-xl px-3 py-2 text-indigo-300 transition hover:bg-slate-800">
        <span>Тести</span>
        <span class="text-xs text-slate-500">●</span>
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
    @if (session('status'))
        <div class="mb-6 rounded-2xl border border-emerald-500 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
            {{ session('status') }}
        </div>
    @endif

    <div class="space-y-4">
        @forelse ($tests as $test)
            @php($attempt = $attempts->get($test->id))
            <article class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg">
                <header class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-white">{{ $test->title }}</h2>
                        <p class="text-xs text-slate-400">
                            @if ($test->lesson)
                                Урок: {{ $test->lesson->title }} ·
                            @endif
                            Викладач: {{ $test->teacher?->full_name ?? '—' }}
                        </p>
                    </div>
                    <div class="text-right text-xs text-slate-400">
                        <p>Питань: {{ $test->questions_count }}</p>
                        <p>Дедлайн: {{ optional($test->due_at)?->diffForHumans() ?? 'немає' }}</p>
                    </div>
                </header>
                <p class="mt-3 text-sm text-slate-300">{{ $test->description }}</p>

                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 text-sm">
                    <span class="rounded-full border border-slate-700 px-3 py-1 text-slate-300">
                        Статус: {{ $attempt?->status === 'submitted' ? 'Завершено' : ($attempt ? 'Розпочато' : 'Не розпочато') }}
                    </span>
                    <span class="text-xs text-slate-400">Макс. балів: {{ $test->max_score }}</span>
                </div>

                <footer class="mt-6 flex flex-wrap gap-3">
                    @if ($attempt && $attempt->status === 'submitted')
                        <span class="inline-flex items-center rounded-full bg-slate-800 px-5 py-2 text-sm font-semibold text-slate-300">
                            Ви вже пройшли тест
                        </span>
                        <span class="text-sm text-emerald-300">Набрано: {{ $attempt->score }} / {{ $attempt->max_score }}</span>
                    @else
                        <a href="{{ route('dashboard.student.tests.show', $test) }}" class="inline-flex items-center rounded-full bg-indigo-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500">
                            Пройти тест
                        </a>
                    @endif
                </footer>
            </article>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-700 bg-slate-900/40 px-6 py-8 text-center text-sm text-slate-400">
                Тестів ще не призначено. Перевіряйте пізніше.
            </div>
        @endforelse
    </div>
@endsection
