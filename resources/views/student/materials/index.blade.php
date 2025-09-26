@extends('layouts.dashboard')

@section('title', 'Матеріали')
@section('panel-label', 'Учень')
@section('heading', 'Матеріали уроків')
@section('subheading', 'Ознайомтеся з уроками та повʼязаними матеріалами')

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
    <a href="{{ route('dashboard.student.materials.index') }}" class="flex items-center justify-between rounded-xl px-3 py-2 text-indigo-300 transition hover:bg-slate-800">
        <span>Матеріали</span>
        <span class="text-xs text-slate-500">●</span>
    </a>
@endsection

@section('content')
    <div class="space-y-5">
        @forelse ($lessons as $lesson)
            <article class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg">
                <header class="flex flex-wrap items-start justify-between gap-4">
                    <div class="space-y-2">
                        <h2 class="text-xl font-semibold text-white">{{ $lesson->title }}</h2>
                        @if ($lesson->topic)
                            <p class="text-sm text-indigo-300">Тема: {{ $lesson->topic }}</p>
                        @endif
                        <p class="text-xs text-slate-400">
                            Викладач: {{ $lesson->teacher?->full_name ?? '—' }}
                        </p>
                        <p class="text-xs text-slate-500">
                            Матеріалів: {{ $lesson->materials->count() }} · Оновлено {{ $lesson->updated_at?->diffForHumans() ?? 'щойно' }}
                        </p>
                    </div>
                    <div class="text-right text-xs text-slate-400 space-y-1">
                        @if ($lesson->starts_at)
                            <p>Дата: {{ $lesson->starts_at->translatedFormat('d MMMM, HH:mm') }}</p>
                        @endif
                        @if ($lesson->duration_minutes)
                            <p>Тривалість: {{ $lesson->duration_minutes }} хв</p>
                        @endif
                        @if ($lesson->meeting_url)
                            <a href="{{ $lesson->meeting_url }}" target="_blank" rel="noopener" class="inline-flex items-center text-indigo-400">Посилання на зустріч →</a>
                        @endif
                    </div>
                </header>

                @if ($lesson->description)
                    <p class="mt-4 text-sm text-slate-300">{{ \Illuminate\Support\Str::limit($lesson->description, 220) }}</p>
                @endif

                <footer class="mt-6 flex flex-wrap justify-between gap-3">
                    <a href="{{ route('dashboard.student.materials.show', $lesson) }}" class="inline-flex items-center rounded-full bg-indigo-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500">
                        Відкрити урок
                    </a>
                    <span class="text-xs text-slate-500">Створено {{ $lesson->created_at?->diffForHumans() ?? 'сьогодні' }}</span>
                </footer>
            </article>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-700 bg-slate-900/40 px-6 py-12 text-center text-sm text-slate-400">
                Поки немає доступних уроків. Повертайтесь пізніше.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $lessons->links('pagination::tailwind') }}
    </div>
@endsection
