@extends('layouts.dashboard')

@php
    use App\Enums\HomeworkStatus;

    $submissionImages = collect($submission?->images ?? [])->unique()->values();
@endphp

@section('title', $homework->title)
@section('panel-label', 'Учень')
@section('heading', $homework->title)
@section('subheading', $homework->lesson?->title ?? 'Домашнє завдання')

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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const MAX_FILES = 7;
            const INITIAL_EXISTING_COUNT = Number({{ $submissionImages->count() }});

            const input = document.getElementById('homework-images');
            const countLabel = document.getElementById('homework-images-count');
            const previewWrapper = document.getElementById('homework-images-preview');
            const previewGrid = previewWrapper ? previewWrapper.querySelector('.grid') : null;
            const warningLabel = document.getElementById('homework-images-warning');
            const existingPhotosWrapper = document.getElementById('homework-existing-photos');
            const removeCheckboxes = existingPhotosWrapper ? Array.from(existingPhotosWrapper.querySelectorAll('input[name="remove_images[]"]')) : [];

            if (!input || !previewWrapper || !previewGrid || !countLabel || !warningLabel) {
                return;
            }

            let pendingFiles = [];
            let previewObjectUrls = [];

            const syncInput = () => {
                const dataTransfer = new DataTransfer();
                pendingFiles.forEach((file) => dataTransfer.items.add(file));
                input.files = dataTransfer.files;
            };

            const getRemainingExisting = () => {
                if (removeCheckboxes.length === 0) {
                    return INITIAL_EXISTING_COUNT;
                }

                const removedCount = removeCheckboxes.filter((checkbox) => checkbox.checked).length;
                return Math.max(INITIAL_EXISTING_COUNT - removedCount, 0);
            };

            const getAvailableSlots = () => Math.max(MAX_FILES - getRemainingExisting() - pendingFiles.length, 0);

            const autoMarkExistingForRemoval = () => {
                if (pendingFiles.length === 0 || removeCheckboxes.length === 0) {
                    return;
                }

                removeCheckboxes.forEach((checkbox) => {
                    if (checkbox.dataset.manual !== 'true') {
                        checkbox.checked = true;
                    }
                });
            };

            const renderPreview = (skipped = 0) => {
                previewObjectUrls.forEach((url) => URL.revokeObjectURL(url));
                previewObjectUrls = [];
                previewGrid.innerHTML = '';

                if (pendingFiles.length === 0) {
                    previewWrapper.classList.add('hidden');
                    removeCheckboxes.forEach((checkbox) => {
                        if (checkbox.dataset.manual !== 'true') {
                            checkbox.checked = false;
                        }
                    });
                } else {
                    previewWrapper.classList.remove('hidden');

                    pendingFiles.forEach((file, index) => {
                        const figure = document.createElement('figure');
                        figure.className = 'relative overflow-hidden rounded-2xl border border-slate-800';

                        const img = document.createElement('img');
                        img.className = 'h-36 w-full object-cover';
                        const objectUrl = URL.createObjectURL(file);
                        previewObjectUrls.push(objectUrl);
                        img.src = objectUrl;
                        img.alt = file.name;

                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'absolute right-3 top-3 rounded-full bg-rose-500/90 px-2 py-1 text-[11px] font-semibold uppercase tracking-wide text-white transition hover:bg-rose-400';
                        button.textContent = 'Видалити';
                        button.addEventListener('click', () => {
                            URL.revokeObjectURL(objectUrl);
                            pendingFiles = pendingFiles.filter((_, fileIndex) => fileIndex !== index);
                            syncInput();
                            renderPreview();
                        });

                        figure.appendChild(img);
                        figure.appendChild(button);
                        previewGrid.appendChild(figure);
                    });
                }

                const maxNewAllowed = Math.max(MAX_FILES - getRemainingExisting(), 0);
                countLabel.textContent = `${pendingFiles.length} / ${maxNewAllowed}`;

                const availableSlots = getAvailableSlots();
                warningLabel.classList.toggle('hidden', availableSlots > 0 && skipped === 0);

                if (skipped > 0) {
                    warningLabel.textContent = `Додано тільки доступні файли. Залиште місце, щоб надіслати ще.`;
                } else if (availableSlots <= 0) {
                    warningLabel.textContent = 'Досягнуто обмеження у 7 фотографій. Позначте зайві або видаліть нові.';
                } else {
                    warningLabel.textContent = '';
                }
            };

            const handleFiles = (files) => {
                let skipped = 0;

                files.forEach((file) => {
                    if (pendingFiles.length >= MAX_FILES) {
                        skipped += 1;
                        return;
                    }

                    if (getAvailableSlots() <= 0) {
                        skipped += 1;
                        return;
                    }

                    pendingFiles.push(file);
                });

                return skipped;
            };

            input.addEventListener('change', () => {
                const files = Array.from(input.files ?? []);
                const skipped = handleFiles(files);
                autoMarkExistingForRemoval();
                input.value = '';
                syncInput();
                renderPreview(skipped);
            });

            removeCheckboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', () => {
                    checkbox.dataset.manual = 'true';
                    renderPreview();
                });
            });

            renderPreview();
        });
    </script>
@endpush

@section('content')
    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-2xl border border-emerald-500 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-rose-500 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">
                <ul class="list-disc space-y-1 pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @php
            $statusEnum = HomeworkStatus::tryFrom($pivot?->status ?? '') ?? HomeworkStatus::ASSIGNED;
            $statusLabel = $statusEnum->label();
            $statusClass = match ($statusEnum) {
                HomeworkStatus::COMPLETED => 'text-emerald-300',
                HomeworkStatus::SUBMITTED => 'text-amber-300',
                HomeworkStatus::VIEWED => 'text-sky-300',
                HomeworkStatus::REDO => 'text-rose-300',
                default => 'text-slate-300',
            };
            $submissionStatus = $submission?->status;
        @endphp

        <section class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg">
            <header class="flex flex-wrap items-start justify-between gap-4">
                <div class="space-y-2">
                    <h1 class="text-2xl font-semibold text-white">{{ $homework->title }}</h1>
                    @if ($homework->lesson && $homework->lesson->topic)
                        <p class="text-sm text-indigo-300">{{ $homework->lesson->topic }}</p>
                    @endif
                    <p class="text-xs text-slate-400">Викладач: {{ $homework->teacher?->full_name ?? '—' }}</p>
                </div>
                <div class="text-right text-xs text-slate-400 space-y-1">
                    <p>Дедлайн: {{ optional($homework->due_at)?->translatedFormat('d MMM yyyy HH:mm') ?? 'не встановлено' }}</p>
                    <p>Статус: <span class="{{ $statusClass }}">{{ $statusLabel }}</span></p>
                    <p>Оновлено {{ $homework->updated_at?->diffForHumans() ?? 'щойно' }}</p>
                </div>
            </header>

            @if ($homework->description)
                <div class="mt-4 text-sm text-slate-300">{{ $homework->description }}</div>
            @endif
        </section>

        <section class="space-y-4">
            @if ($homework->content)
                <article class="rounded-3xl border border-slate-800 bg-slate-900 p-5 shadow-lg">
                    <h2 class="text-lg font-semibold text-white">Інструкція</h2>
                    <div class="mt-3 prose prose-invert max-w-none text-sm">{!! $homework->content !!}</div>
                </article>
            @endif

            @php
                $videos = collect($homework->videos ?? []);
                if ($videos->isEmpty() && $homework->external_url) {
                    $videos = collect([[
                        'label' => null,
                        'url' => $homework->external_url,
                        'note' => null,
                    ]]);
                }
            @endphp

            @if ($videos->isNotEmpty())
                <article class="rounded-3xl border border-slate-800 bg-slate-900 p-5 shadow-lg space-y-4">
                    <h2 class="text-lg font-semibold text-white">Відео та посилання</h2>
                    @foreach ($videos as $video)
                        @php
                            $videoUrl = $video['url'] ?? null;
                            $embedUrl = null;
                            if ($videoUrl) {
                                if (str_contains($videoUrl, 'youtube.com/watch')) {
                                    parse_str(parse_url($videoUrl, PHP_URL_QUERY) ?? '', $queryParams);
                                    if (! empty($queryParams['v'])) {
                                        $embedUrl = 'https://www.youtube-nocookie.com/embed/' . $queryParams['v'];
                                    }
                                } elseif (preg_match('~youtu\.be/([^?&]+)~', $videoUrl, $matches)) {
                                    $embedUrl = 'https://www.youtube-nocookie.com/embed/' . $matches[1];
                                }
                            }
                        @endphp
                        <div class="space-y-2">
                            @if (! empty($video['label']))
                                <h3 class="text-sm font-semibold text-white">{{ $video['label'] }}</h3>
                            @endif
                            @if ($embedUrl)
                                <div class="relative w-full overflow-hidden rounded-2xl border border-slate-800 pb-[56.25%]">
                                    <iframe
                                        src="{{ $embedUrl }}"
                                        class="absolute inset-0 h-full w-full"
                                        title="Відео домашнього завдання"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen
                                    ></iframe>
                                </div>
                            @endif
                            @if ($videoUrl)
                                <a href="{{ $videoUrl }}" target="_blank" rel="noopener" class="inline-flex items-center rounded-full bg-indigo-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-indigo-500">
                                    Відкрити посилання
                                </a>
                            @endif
                            @if (! empty($video['note']))
                                <p class="text-xs text-slate-400">{{ $video['note'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </article>
            @endif

            @php
                $imagePaths = collect($homework->images ?? []);
            @endphp

            @if ($imagePaths->isNotEmpty())
                <article class="rounded-3xl border border-slate-800 bg-slate-900 p-5 shadow-lg space-y-4">
                    <h2 class="text-lg font-semibold text-white">Зображення</h2>
                    <div class="grid gap-3 md:grid-cols-2">
                        @foreach ($imagePaths as $imagePath)
                            @php
                                $imageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($imagePath);
                            @endphp
                            <figure class="overflow-hidden rounded-2xl border border-slate-800">
                                <img src="{{ $imageUrl }}" alt="{{ $homework->title }}" class="w-full object-cover">
                            </figure>
                        @endforeach
                    </div>
                </article>
            @endif

            @php
                $attachmentPaths = collect($homework->attachments ?? [])->filter();
            @endphp

            @if ($attachmentPaths->isNotEmpty())
                <article class="rounded-3xl border border-slate-800 bg-slate-900 p-5 shadow-lg space-y-3">
                    <h2 class="text-lg font-semibold text-white">Додаткові файли</h2>
                    <ul class="space-y-2 text-sm text-slate-300">
                        @foreach ($attachmentPaths as $path)
                            @php
                                $fileUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($path);
                                $name = basename($path);
                            @endphp
                            <li class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-slate-800/60 px-4 py-3">
                                <span>{{ $name }}</span>
                                <div class="flex flex-wrap items-center gap-3">
                                    <a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="inline-flex items-center rounded-full bg-slate-800 px-4 py-2 text-sm text-indigo-300 transition hover:bg-slate-700">
                                        Відкрити
                                    </a>
                                    @if ($homework->is_downloadable)
                                        <a href="{{ $fileUrl }}" download class="text-xs text-slate-400">Завантажити</a>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </article>
            @endif

            @if (! empty($homework->resource_links))
                <article class="rounded-3xl border border-slate-800 bg-slate-900 p-5 shadow-lg space-y-3">
                    <h2 class="text-lg font-semibold text-white">Джерела</h2>
                    <ul class="space-y-2 text-sm text-slate-300">
                        @foreach ($homework->resource_links as $link)
                            <li class="rounded-2xl bg-slate-800/60 px-4 py-3">
                                <a href="{{ $link['url'] ?? '#' }}" target="_blank" rel="noopener" class="font-semibold text-indigo-300">{{ $link['label'] ?? ($link['url'] ?? 'Посилання') }}</a>
                                @if (! empty($link['note']))
                                    <p class="text-xs text-slate-400">{{ $link['note'] }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </article>
            @endif

            <article class="rounded-3xl border border-slate-800 bg-slate-900 p-5 shadow-lg space-y-4">
                <h2 class="text-lg font-semibold text-white">Моє рішення</h2>

                @if ($submissionStatus instanceof HomeworkStatus)
                    @php
                        $submissionStatusClass = match ($submissionStatus) {
                            HomeworkStatus::COMPLETED => 'border-emerald-500/40 bg-emerald-500/10 text-emerald-100',
                            HomeworkStatus::REDO => 'border-rose-500/40 bg-rose-500/10 text-rose-100',
                            HomeworkStatus::SUBMITTED => 'border-amber-500/40 bg-amber-500/10 text-amber-100',
                            HomeworkStatus::VIEWED => 'border-sky-500/40 bg-sky-500/10 text-sky-100',
                            default => 'border-slate-700 bg-slate-800/40 text-slate-200',
                        };
                    @endphp
                    <div class="rounded-2xl border px-4 py-3 text-xs font-semibold {{ $submissionStatusClass }}">
                        Статус перевірки: {{ $submissionStatus->label() }}
                        @if ($submission?->feedback_left_at)
                            <span class="ml-2 font-normal text-[11px] text-slate-300">{{ $submission->feedback_left_at->diffForHumans() }}</span>
                        @endif
                    </div>
                @endif

                @if ($submission?->teacher_feedback)
                    <div class="space-y-2 rounded-2xl border border-emerald-500/30 bg-emerald-500/10 px-4 py-3">
                        <div class="text-xs font-semibold uppercase tracking-wide text-emerald-200">Відгук викладача</div>
                        <div class="whitespace-pre-wrap text-sm text-emerald-100">{{ $submission->teacher_feedback }}</div>
                        <div class="text-[11px] text-emerald-200/70">Оновлено {{ optional($submission->feedback_left_at)?->diffForHumans() ?? 'щойно' }}</div>
                    </div>
                @endif

                @if ($submission)
                    <div class="space-y-3 rounded-2xl border border-slate-800 bg-slate-800/50 px-4 py-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="text-xs text-slate-400">Надіслано: {{ optional($submission->submitted_at)?->diffForHumans() ?? 'щойно' }}</p>
                            <p class="text-[11px] text-slate-500">Фото: {{ $submissionImages->count() }}</p>
                        </div>
                        @if ($submission->body)
                            <div class="prose prose-invert max-w-none text-sm">{!! nl2br(e($submission->body)) !!}</div>
                        @endif
                    </div>
                @endif

                <form method="POST" action="{{ route('dashboard.student.homework.submit', $homework) }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <div>
                        <label for="body" class="block text-sm font-semibold text-slate-200">Відповідь</label>
                        <textarea id="body" name="body" rows="5" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-slate-200 focus:border-indigo-400 focus:outline-none" placeholder="Опишіть виконання...">{{ old('body', $submission->body ?? '') }}</textarea>
                    </div>
                    @if ($submissionImages->isNotEmpty())
                        <div id="homework-existing-photos" class="space-y-3 rounded-2xl border border-slate-800 bg-slate-900/60 px-4 py-4" data-existing-count="{{ $submissionImages->count() }}">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <h3 class="text-sm font-semibold text-white">Мої фотографії</h3>
                                <p class="text-xs text-slate-400">Натисніть, щоб залишити або видалити перед повторною відправкою</p>
                            </div>
                            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
                                @foreach ($submissionImages as $path)
                                    @php($url = \Illuminate\Support\Facades\Storage::disk('public')->url($path))
                                    @php($markedForRemoval = in_array($path, old('remove_images', []), true))
                                    <label class="group relative block overflow-hidden rounded-2xl border border-slate-800">
                                        <input type="checkbox" name="remove_images[]" value="{{ $path }}" class="peer absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0" @checked($markedForRemoval)>
                                        <img src="{{ $url }}" alt="Поточне фото" class="h-40 w-full object-cover transition group-hover:opacity-80 peer-checked:scale-95 peer-checked:opacity-40">
                                        <span class="pointer-events-none absolute inset-x-0 bottom-0 flex h-10 items-center justify-center bg-slate-900/80 text-xs font-semibold text-rose-200 opacity-0 transition group-hover:opacity-100 peer-checked:opacity-100">
                                            Фото буде видалено
                                        </span>
                                        <span class="pointer-events-none absolute right-3 top-3 rounded-full bg-rose-500/90 px-2 py-1 text-[10px] font-semibold uppercase tracking-wide text-white opacity-0 transition group-hover:opacity-100 peer-checked:opacity-100">
                                            Видалити
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-[11px] text-slate-500">Після надсилання позначені фото буде видалено назавжди.</p>
                        </div>
                    @endif
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <label for="homework-images" class="text-sm font-semibold text-slate-200">Нові фотографії (до 7, максимум 3 МБ кожне)</label>
                            <span class="text-[11px] text-slate-500">Нові фото: <span id="homework-images-count">0</span></span>
                        </div>
                        <input id="homework-images" type="file" name="images[]" multiple accept="image/*" class="block w-full cursor-pointer text-sm text-slate-300 file:mr-4 file:rounded-full file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-indigo-500">
                        <p class="text-xs text-slate-500">Після вибору нових фото поточні автоматично позначаються до видалення — зніміть позначку, якщо хочете їх залишити. Загальна кількість після надсилання не перевищує 7.</p>
                        <div id="homework-images-preview" class="hidden space-y-3 rounded-2xl border border-slate-800 bg-slate-900/40 px-4 py-4">
                            <p class="text-xs font-semibold text-slate-300">Попередній перегляд</p>
                            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-3"></div>
                            <p id="homework-images-warning" class="hidden text-xs text-rose-300"></p>
                        </div>
                    </div>
                    <button type="submit" class="inline-flex items-center rounded-full bg-emerald-500 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-400">
                        @if ($submissionStatus instanceof HomeworkStatus && $submissionStatus === HomeworkStatus::REDO)
                            Повторно надіслати на перевірку
                        @elseif ($submission)
                            Оновити рішення
                        @else
                            Надіслати викладачу
                        @endif
                    </button>
                </form>
            </article>
        </section>

        <footer class="flex justify-start">
            <a href="{{ route('dashboard.student.homework.index') }}" class="inline-flex items-center rounded-full bg-slate-800 px-5 py-2 text-sm font-semibold text-slate-300 transition hover:bg-slate-700">
                ← Повернутися до списку завдань
            </a>
        </footer>
    </div>
@endsection
