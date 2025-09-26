<?php use Illuminate\Support\Facades\Storage; ?>
<div class="space-y-4 text-sm text-slate-300">
    <div class="grid gap-2 rounded-2xl bg-slate-900/50 px-4 py-3">
        <div class="text-xs uppercase tracking-wide text-slate-500">Учень</div>
        <div class="font-semibold text-white">{{ $submission->student?->full_name ?: '—' }}</div>
        @if ($submission->student?->username)
            <div class="text-xs text-slate-500">Логін: {{ $submission->student->username }}</div>
        @endif
        <div class="text-xs text-slate-400">{{ $submission->student?->email }}</div>
    </div>

    <div class="grid gap-2 rounded-2xl bg-slate-900/50 px-4 py-3">
        <div class="text-xs uppercase tracking-wide text-slate-500">Домашнє завдання</div>
        <div class="font-semibold text-white">{{ $submission->homework?->title ?? '—' }}</div>
        @if ($submission->homework?->lesson)
            <div class="text-xs text-slate-400">Урок: {{ $submission->homework->lesson->title }}</div>
        @endif
        <div class="text-xs text-slate-500">Надіслано: {{ optional($submission->submitted_at)?->translatedFormat('d MMM yyyy HH:mm') ?? '—' }}</div>
    </div>

    @php
        $statusColor = match ($submission->status?->value ?? null) {
            'completed' => 'text-emerald-300',
            'redo' => 'text-rose-300',
            'submitted' => 'text-amber-300',
            'viewed' => 'text-sky-300',
            default => 'text-slate-200',
        };
    @endphp
    <div class="grid gap-2 rounded-2xl bg-slate-900/50 px-4 py-3">
        <div class="text-xs uppercase tracking-wide text-slate-500">Статус</div>
        <div class="font-semibold {{ $statusColor }}">
            {{ $submission->statusLabel() }}
        </div>
        @if ($submission->feedback_left_at)
            <div class="text-xs text-slate-500">Оновлено: {{ $submission->feedback_left_at->diffForHumans() }}</div>
        @endif
    </div>

    @if ($submission->body)
        <div class="grid gap-2 rounded-2xl bg-slate-900/50 px-4 py-3">
            <div class="text-xs uppercase tracking-wide text-slate-500">Відповідь</div>
            <div class="whitespace-pre-wrap text-slate-200">{{ $submission->body }}</div>
        </div>
    @endif

    @if ($submission->teacher_feedback)
        <div class="grid gap-2 rounded-2xl bg-slate-900/50 px-4 py-3">
            <div class="text-xs uppercase tracking-wide text-slate-500">Відгук викладача</div>
            <div class="whitespace-pre-wrap text-slate-200">{{ $submission->teacher_feedback }}</div>
        </div>
    @endif

    @if (is_array($submission->images) && count($submission->images))
        <div class="grid gap-3 rounded-2xl bg-slate-900/50 px-4 py-3">
            <div class="text-xs uppercase tracking-wide text-slate-500">Фотографії</div>
            <div class="grid gap-3 md:grid-cols-2">
                @foreach ($submission->images as $path)
                    <figure class="overflow-hidden rounded-2xl border border-slate-800">
                        <img src="{{ Storage::disk('public')->url($path) }}" alt="Фото" class="w-full object-cover">
                    </figure>
                @endforeach
            </div>
        </div>
    @endif

    @if (is_array($submission->attachments) && count($submission->attachments))
        <div class="grid gap-2 rounded-2xl bg-slate-900/50 px-4 py-3">
            <div class="text-xs uppercase tracking-wide text-slate-500">Додані файли</div>
            <ul class="space-y-2 text-xs text-indigo-300">
                @foreach ($submission->attachments as $path)
                    <li>
                        <a href="{{ Storage::disk('public')->url($path) }}" target="_blank" rel="noopener" class="underline hover:text-indigo-200">
                            {{ basename($path) }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
