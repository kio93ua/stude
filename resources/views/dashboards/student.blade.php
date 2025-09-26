@extends('layouts.dashboard')

@section('title', 'Кабінет учня')
@section('panel-label', 'Учень')
@section('heading', 'Мій прогрес')
@section('subheading', 'Уроки, домашні завдання та рекомендації викладача')

@section('sidebar')
    <a href="#" class="flex items-center justify-between rounded-xl px-3 py-2 text-indigo-300 transition hover:bg-slate-800">
        <span>Головна</span>
        <span class="text-xs text-slate-500">●</span>
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
    <a href="{{ route('dashboard.student.vocabulary.index') }}" class="flex items-center justify-between rounded-xl px-3 py-2 transition hover:bg-slate-800">
        <span>Словник</span>
        <span class="text-xs text-slate-500">›</span>
    </a>
    <a href="#" class="flex items-center justify-between rounded-xl px-3 py-2 transition hover:bg-slate-800">
        <span>Speaking club</span>
        <span class="text-xs text-slate-500">›</span>
    </a>
@endsection

@section('content')
    @if (! $student)
        <div class="rounded-3xl border border-slate-800 bg-slate-900 p-6 text-sm text-slate-300">
            Немає доступних учнівських даних. Створіть користувача з роллю <span class="font-semibold text-white">student</span> і додайте записи через панель викладача.
        </div>
    @else
        <div class="mb-8 rounded-3xl border border-slate-800 bg-slate-900 p-6 text-sm text-slate-300">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-xs uppercase tracking-wide text-slate-400">Ваш логін</p>
                    <p class="text-lg font-semibold text-white">{{ $student->username }}</p>
                    @if ($student->full_name && $student->full_name !== $student->username)
                        <p class="mt-1 text-xs text-slate-400">Ім'я: {{ $student->full_name }}</p>
                    @endif
                </div>
                <div class="text-xs text-slate-400">Уроків у розкладі: {{ $progressMetrics['lessonsTotal'] }} · Завершено: {{ $progressMetrics['lessonsCompleted'] }}</div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
            <section class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg">
                <h2 class="text-lg font-semibold text-white">Домашні завдання</h2>
                <ul class="mt-5 space-y-4 text-sm text-slate-300">
                    @forelse ($homeworks as $homework)
                        <li class="rounded-2xl border border-slate-800 bg-slate-800/60 px-4 py-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-white">{{ $homework->title }}</p>
                                    @if ($homework->description)
                                        <p class="text-xs text-slate-400">{{ \Illuminate\Support\Str::limit($homework->description, 90) }}</p>
                                    @endif
                                </div>
                                <span class="text-xs text-slate-400">{{ optional($homework->due_at)?->translatedFormat('d MMM') ?? 'без дедлайну' }}</span>
                            </div>
                            @php
                                $assignmentPivot = $homework->students->first()?->pivot;
                                $statusEnum = \App\Enums\HomeworkStatus::tryFrom($assignmentPivot?->status ?? '') ?? \App\Enums\HomeworkStatus::ASSIGNED;
                                $statusLabel = $statusEnum->label();
                                $statusClass = match ($statusEnum) {
                                    \App\Enums\HomeworkStatus::COMPLETED => 'text-emerald-300',
                                    \App\Enums\HomeworkStatus::SUBMITTED => 'text-amber-300',
                                    \App\Enums\HomeworkStatus::VIEWED => 'text-sky-300',
                                    \App\Enums\HomeworkStatus::REDO => 'text-rose-300',
                                    default => 'text-slate-300',
                                };
                            @endphp
                            <p class="mt-1 text-xs {{ $statusClass }}">Статус: {{ $statusLabel }}</p>
                            <a href="{{ route('dashboard.student.homework.show', $homework) }}" class="mt-2 inline-flex items-center text-xs font-semibold text-indigo-400">Перейти до завдання →</a>
                        </li>
                    @empty
                        <li class="rounded-2xl border border-dashed border-slate-700 bg-slate-900/40 px-4 py-6 text-center text-xs text-slate-400">
                            Немає призначених домашніх завдань. Очікуйте нові від викладача.
                        </li>
                    @endforelse
                </ul>
                <div class="mt-6 border-t border-slate-800 pt-6">
                    <h3 class="text-sm font-semibold text-white">Матеріали до уроків</h3>
                    <ul class="mt-3 space-y-3 text-xs text-slate-300">
                        @forelse ($materials as $material)
                            @php
                                $typeFlags = [];
                                if (! empty($material->content)) {
                                    $typeFlags[] = 'Текст';
                                }
                                if (! empty($material->external_url) || ! empty($material->thumbnail_path)) {
                                    $typeFlags[] = 'Відео/посилання';
                                }
                                if (! empty($material->resource_links)) {
                                    $typeFlags[] = 'Джерела';
                                }
                                $imagePaths = collect($material->images ?? []);
                                if ($imagePaths->isNotEmpty()) {
                                    $typeFlags[] = 'Зображення';
                                }
                                $attachmentsAvailable = ! empty($material->attachments) || ! empty($material->file_url);
                                if ($attachmentsAvailable) {
                                    $typeFlags[] = 'Файли';
                                }

                                $downloadUrl = null;
                                if ($attachmentsAvailable) {
                                    $firstPath = collect($material->attachments ?? [])->filter()->first() ?? $material->file_url;
                                    if ($firstPath) {
                                        $downloadUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($firstPath);
                                    }
                                }
                                if (! $downloadUrl && $imagePaths->isNotEmpty()) {
                                    $downloadUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($imagePaths->first());
                                }
                                if (! $downloadUrl && ! empty($material->external_url)) {
                                    $downloadUrl = $material->external_url;
                                }
                            @endphp
                            <li class="rounded-2xl bg-slate-800/60 px-4 py-3">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="space-y-1 text-xs text-slate-300">
                                        <p class="text-sm font-semibold text-white">{{ $material->title }}</p>
                                        <p class="text-[11px] text-slate-400">
                                            {{ $material->lesson?->title }}
                                            @if ($material->lesson?->topic)
                                                · {{ $material->lesson->topic }}
                                            @endif
                                        </p>
                                        <p class="text-[11px] text-slate-500">Вміст: {{ implode(', ', $typeFlags) ?: 'Матеріал' }}</p>
                                        @if ($material->description)
                                            <p class="text-[11px] text-slate-400">{{ \Illuminate\Support\Str::limit($material->description, 140) }}</p>
                                        @endif
                                        @if (! empty($material->content))
                                            <details class="mt-2">
                                                <summary class="cursor-pointer text-indigo-400">Показати текст</summary>
                                                <div class="prose prose-invert mt-2 max-w-none text-xs">{!! $material->content !!}</div>
                                            </details>
                                        @endif
                                    </div>
                                    @if ($downloadUrl)
                                        <a href="{{ $downloadUrl }}" target="_blank" rel="noopener" class="text-indigo-400">Відкрити</a>
                                    @else
                                        <span class="text-[11px] text-slate-500">Зверніться до викладача для доступу</span>
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li class="rounded-2xl border border-dashed border-slate-700 bg-slate-900/40 px-4 py-4 text-center text-xs text-slate-400">Матеріали ще не додані.</li>
                        @endforelse
                    </ul>
                    @if ($materials->isEmpty() && isset($availableLessons))
                        <div class="mt-4 rounded-2xl border border-slate-800 bg-slate-900/60 p-4 text-xs text-slate-300">
                            <h4 class="text-sm font-semibold text-white">Доступні уроки</h4>
                            <ul class="mt-3 space-y-3">
                                @forelse ($availableLessons as $lesson)
                                    <li class="rounded-xl bg-slate-800/60 px-4 py-3">
                                        <p class="text-sm font-semibold text-white">{{ $lesson->title }}</p>
                                        <p class="text-[11px] text-slate-400">
                                            @if ($lesson->topic)
                                                {{ $lesson->topic }} ·
                                            @endif
                                            Викладач: {{ $lesson->teacher?->full_name ?? '—' }}
                                        </p>
                                        @if ($lesson->description)
                                            <p class="mt-2 text-[11px] text-slate-500">{{ \Illuminate\Support\Str::limit($lesson->description, 160) }}</p>
                                        @endif
                                    </li>
                                @empty
                                    <li class="rounded-xl border border-dashed border-slate-700 px-4 py-3 text-center text-[11px] text-slate-500">Поки немає доступних уроків.</li>
                                @endforelse
                            </ul>
                        </div>
                    @endif
                </div>
            </section>
            <section class="space-y-6">
                <div class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg">
                    <h2 class="text-lg font-semibold text-white">Прогрес</h2>
                    <div class="mt-4 space-y-3 text-xs text-slate-300">
                        <div>
                            <div class="flex items-center justify-between">
                                <span>Загальний</span>
                                <span class="text-slate-400">{{ $progressMetrics['overall'] }}%</span>
                            </div>
                            <div class="mt-2 h-2 rounded-full bg-slate-800">
                                <div class="h-full rounded-full bg-indigo-500" style="width: {{ $progressMetrics['overall'] }}%"></div>
                            </div>
                        </div>
                        @php($categories = $progressMetrics['categories'])
                        @forelse ($categories as $category => $value)
                            <div>
                                <div class="flex items-center justify-between">
                                    <span>{{ \Illuminate\Support\Str::title($category) }}</span>
                                    <span class="text-slate-400">{{ $value }}%</span>
                                </div>
                                <div class="mt-2 h-2 rounded-full bg-slate-800">
                                    <div class="h-full rounded-full bg-emerald-500" style="width: {{ $value }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400">Немає ще оцінених тестів — прогрес з’явиться після першого результату.</p>
                        @endforelse
                    </div>
                </div>
                <div class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg">
                    <h2 class="text-lg font-semibold text-white">Повідомлення від викладача</h2>
                    <ul class="mt-4 space-y-3 text-xs text-slate-300">
                        @forelse ($messages as $message)
                            <li class="rounded-2xl bg-slate-800/60 px-4 py-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <span class="text-sm font-semibold text-white">{{ $message->sender?->username ?? 'Викладач' }}</span>
                                        @if ($message->sender && $message->sender->full_name && $message->sender->full_name !== $message->sender->username)
                                            <p class="text-[11px] text-slate-500">ПІБ: {{ $message->sender->full_name }}</p>
                                        @endif
                                    </div>
                                    <span class="text-[10px] text-slate-500">{{ optional($message->sent_at ?? $message->created_at)?->diffForHumans() }}</span>
                                </div>
                                <p class="mt-2 text-slate-400">{{ \Illuminate\Support\Str::limit($message->body, 160) }}</p>
                            </li>
                        @empty
                            <li class="rounded-2xl border border-dashed border-slate-700 bg-slate-900/40 px-4 py-6 text-center text-xs text-slate-400">
                                Повідомлень поки немає.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </section>
        </div>
    @endif
@endsection
