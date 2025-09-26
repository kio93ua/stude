@extends('layouts.dashboard')

@section('title', 'Домашні завдання')
@section('panel-label', 'Учень')
@section('heading', 'Домашні завдання')
@section('subheading', 'Усі поточні та минулі завдання, призначені вам')

@section('sidebar')
    <a href="{{ route('dashboard.student') }}" class="flex items-center justify-between rounded-xl px-3 py-2 transition hover:bg-slate-800">
        <span>Головна</span>
        <span class="text-xs text-slate-500">›</span>
    </a>
    <a href="{{ route('dashboard.student.tests.index') }}" class="flex items-center justify-between rounded-xl px-3 py-2 transition hover:bg-slate-800">
        <span>Тести</span>
        <span class="text-xs text-slate-500">›</span>
    </a>
    <a href="{{ route('dashboard.student.homework.index') }}" class="flex items-center justify-between rounded-xl px-3 py-2 text-indigo-300 transition hover:bg-slate-800">
        <span>Домашні завдання</span>
        <span class="text-xs text-slate-500">●</span>
    </a>
    <a href="{{ route('dashboard.student.materials.index') }}" class="flex items-center justify-between rounded-xl px-3 py-2 transition hover:bg-slate-800">
        <span>Матеріали</span>
        <span class="text-xs text-slate-500">›</span>
    </a>
@endsection

@php use App\Enums\HomeworkStatus; @endphp

@section('content')
    <div class="space-y-4">
        @forelse ($homeworks as $homework)
            @php
                $pivot = $homework->students->first()?->pivot;
                $statusEnum = HomeworkStatus::tryFrom($pivot?->status ?? '') ?? HomeworkStatus::ASSIGNED;
                $statusLabel = $statusEnum->label();
                $statusClass = match ($statusEnum) {
                    HomeworkStatus::COMPLETED => 'text-emerald-300',
                    HomeworkStatus::SUBMITTED => 'text-amber-300',
                    HomeworkStatus::VIEWED => 'text-sky-300',
                    HomeworkStatus::REDO => 'text-rose-300',
                    default => 'text-slate-300',
                };
            @endphp
            <article class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg">
                <header class="flex flex-wrap items-start justify-between gap-4">
                    <div class="space-y-1">
                        <h2 class="text-lg font-semibold text-white">{{ $homework->title }}</h2>
                        @if ($homework->lesson)
                            <p class="text-xs text-slate-400">Урок: {{ $homework->lesson->title }}</p>
                        @endif
                        @if ($homework->description)
                            <p class="text-sm text-slate-300">{{ \Illuminate\Support\Str::limit($homework->description, 140) }}</p>
                        @endif
                    </div>
                    <div class="text-right text-xs text-slate-400 space-y-1">
                        <p>Статус: <span class="{{ $statusClass }}">{{ $statusLabel }}</span></p>
                        <p>Дедлайн: {{ optional($homework->due_at)?->translatedFormat('d MMM yyyy HH:mm') ?? 'не встановлено' }}</p>
                        <p>Оновлено {{ $homework->updated_at?->diffForHumans() ?? 'щойно' }}</p>
                    </div>
                </header>
                <footer class="mt-5 flex flex-wrap gap-3">
                    <a href="{{ route('dashboard.student.homework.show', $homework) }}" class="inline-flex items-center rounded-full bg-indigo-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500">
                        Відкрити завдання
                    </a>
                </footer>
            </article>
        @empty
            <div class="rounded-3xl border border-dashed border-slate-700 bg-slate-900/40 px-6 py-12 text-center text-sm text-slate-400">
                Домашніх завдань поки немає.
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $homeworks->links('pagination::tailwind') }}
    </div>
@endsection
