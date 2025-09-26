@extends('layouts.dashboard')

@section('title', $lesson->title)
@section('panel-label', 'Учень')
@section('heading', $lesson->title)
@section('subheading', $lesson->topic ? 'Тема: ' . $lesson->topic : 'Урок і матеріали')

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
        <span class="text-xs text-сlate-500">›</span>
    </a>
    <a href="{{ route('dashboard.student.materials.index') }}" class="flex items-center justify-between rounded-xl px-3 py-2 text-indigo-300 transition hover:bg-slate-800">
        <span>Матеріали</span>
        <span class="text-xs text-slate-500">●</span>
    </a>
@endsection

@section('content')
    <div class="space-y-6">
        <section class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg">
            <header class="flex flex-wrap items-start justify-between gap-4">
                <div class="space-y-2">
                    <h1 class="text-2xl font-semibold text-white">{{ $lesson->title }}</h1>
                    @if ($lesson->topic)
                        <p class="text-sm text-indigo-300">{{ $lesson->topic }}</p>
                    @endif
                    <p class="text-xs text-slate-400">Викладач: {{ $lesson->teacher?->full_name ?? '—' }}</p>
                </div>
                <div class="text-right text-xs text-slate-400 space-y-1">
                    @if ($lesson->starts_at)
                        <p>Початок: {{ $lesson->starts_at->translatedFormat('d MMMM, HH:mm') }}</p>
                    @endif
                    @if ($lesson->duration_minutes)
                        <p>Тривалість: {{ $lesson->duration_minutes }} хв</p>
                    @endif
                    @if ($lesson->meeting_url)
                        <a href="{{ $lesson->meeting_url }}" target="_blank" rel="noopener" class="inline-flex items-center text-indigo-400">Посилання на зустріч →</a>
                    @endif
                    @if ($lesson->status)
                        <p>Статус: {{ \Illuminate\Support\Str::title($lesson->status) }}</p>
                    @endif
                </div>
            </header>

            @if ($lesson->description)
                <div class="mt-4 text-sm text-slate-300">
                    {{ $lesson->description }}
                </div>
            @endif
        </section>

        <section class="space-y-4">
            <h2 class="text-lg font-semibold text-white">Матеріали</h2>
            @forelse ($lesson->materials as $material)
                <article class="rounded-3xl border border-slate-800 bg-slate-900 p-5 shadow-lg space-y-4">
                    <header class="flex flex-wrap items-start justify-between gap-4">
                        @php
                            $contentTags = [];
                            if (! empty($material->content)) {
                                $contentTags[] = 'Текст';
                            }
                            if (! empty($material->external_url) || ! empty($material->thumbnail_path)) {
                                $contentTags[] = 'Відео/посилання';
                            }
                            if (! empty($material->resource_links)) {
                                $contentTags[] = 'Джерела';
                            }

                            $imagePathsPreview = collect($material->images ?? []);
                            if ($imagePathsPreview->isNotEmpty()) {
                                $contentTags[] = 'Зображення';
                            }

                            $hasAttachments = ! empty($material->attachments) || ! empty($material->file_url);
                            if ($hasAttachments) {
                                $contentTags[] = 'Файли';
                            }
                        $videos = collect($material->videos ?? []);
                        if ($videos->isNotEmpty() || $material->external_url) {
                            $contentTags[] = 'Відео/посилання';
                        }

                        @endphp
                        <div>
                            <h3 class="text-lg font-semibold text-white">{{ $material->title }}</h3>
                            <p class="text-xs text-slate-400">
                                Вміст: {{ implode(', ', $contentTags) ?: 'Матеріал' }}
                            </p>
                        </div>
                        <span class="text-xs text-slate-500">Оновлено {{ $material->updated_at?->diffForHumans() ?? 'щойно' }}</span>
                    </header>

                    @if ($material->description)
                        <p class="text-sm text-slate-300">{{ $material->description }}</p>
                    @endif

                    @if (! empty($material->content))
                        <div class="prose prose-invert max-w-none text-sm">{!! $material->content !!}</div>
                    @endif

                    @php
                        $videos = $videos->isNotEmpty()
                            ? $videos
                            : collect($material->external_url ? [[
                                'label' => null,
                                'url' => $material->external_url,
                                'note' => null,
                            ]] : []);
                    @endphp

                    @if ($videos->isNotEmpty())
                        <div class="space-y-4">
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
                                <div class="space-y-3">
                                    @if ($video['label'] ?? false)
                                        <h4 class="text-sm font-semibold text-white">{{ $video['label'] }}</h4>
                                    @endif
                                    @if ($embedUrl)
                                        <div class="relative w-full overflow-hidden rounded-2xl border border-slate-800 pb-[56.25%]">
                                            <iframe
                                                src="{{ $embedUrl }}"
                                                class="absolute inset-0 h-full w-full"
                                                title="Відео уроку"
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
                                    @if ($video['note'] ?? false)
                                        <p class="text-xs text-slate-400">{{ $video['note'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @php
                        $imagePaths = collect($material->images ?? []);
                    @endphp

                    @if ($imagePaths->isNotEmpty())
                        <div class="grid gap-3 md:grid-cols-2">
                            @foreach ($imagePaths as $imagePath)
                                @php
                                    $imageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($imagePath);
                                @endphp
                                <figure class="overflow-hidden rounded-2xl border border-slate-800">
                                    <img src="{{ $imageUrl }}" alt="{{ $material->title }}" class="w-full object-cover">
                                </figure>
                            @endforeach
                        </div>
                    @endif

                    @php
                        $attachmentPaths = collect($material->attachments ?? [])
                            ->filter()
                            ->values();
                        if ($attachmentPaths->isEmpty() && ! empty($material->file_url)) {
                            $attachmentPaths = collect([$material->file_url]);
                        }
                        $attachmentPaths = $attachmentPaths->reject(fn ($path) => $imagePaths->contains($path));
                    @endphp

                    @if ($attachmentPaths->isNotEmpty())
                        <div class="space-y-3">
                            @foreach ($attachmentPaths as $path)
                                @php
                                    $fileUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($path);
                                @endphp
                                @if (preg_match('~\.(png|jpe?g|gif|webp)$~i', $path))
                                    <img src="{{ $fileUrl }}" alt="{{ $material->title }}" class="max-h-96 rounded-2xl border border-slate-800">
                                @endif
                                <div class="flex flex-wrap items-center gap-3">
                                    <a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="inline-flex items-center rounded-full bg-slate-800 px-4 py-2 text-sm text-indigo-300 transition hover:bg-slate-700">
                                        Переглянути файл
                                    </a>
                                    @if ($material->is_downloadable)
                                        <a href="{{ $fileUrl }}" download class="text-xs text-slate-400">Завантажити</a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if (! empty($material->resource_links))
                        <div class="space-y-2">
                            <h4 class="text-sm font-semibold text-white">Додаткові джерела</h4>
                            <ul class="space-y-2 text-sm text-slate-300">
                                @foreach ($material->resource_links as $link)
                                    <li class="rounded-xl bg-slate-800/60 px-4 py-2">
                                        <a href="{{ $link['url'] ?? '#' }}" target="_blank" rel="noopener" class="font-semibold text-indigo-300">{{ $link['label'] ?? ($link['url'] ?? 'Посилання') }}</a>
                                        @if (! empty($link['note']))
                                            <p class="text-xs text-slate-400">{{ $link['note'] }}</p>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </article>
            @empty
                <div class="rounded-3xl border border-dashed border-slate-700 bg-slate-900/40 px-6 py-8 text-center text-sm text-slate-400">
                    У цього уроку ще немає матеріалів.
                </div>
            @endforelse
        </section>

        <footer class="flex justify-start">
            <a href="{{ route('dashboard.student.materials.index') }}" class="inline-flex items-center rounded-full bg-slate-800 px-5 py-2 text-sm font-semibold text-slate-300 transition hover:bg-slate-700">
                ← Повернутися до списку уроків
            </a>
        </footer>
    </div>
@endsection
