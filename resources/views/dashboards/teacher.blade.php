@extends('layouts.dashboard')

@section('title', 'Кабінет викладача')
@section('panel-label', 'Вчитель')
@section('heading', 'Розклад та заняття')
@section('subheading', 'Всі уроки, домашні завдання та прогрес груп')

@section('sidebar')
    <a href="#" class="flex items-center justify-between rounded-xl px-3 py-2 text-indigo-300 transition hover:bg-slate-800">
        <span>Мій розклад</span>
        <span class="text-xs text-slate-500">●</span>
    </a>
    <a href="#" class="flex items-center justify-between rounded-xl px-3 py-2 transition hover:bg-slate-800">
        <span>Домашні завдання</span>
        <span class="text-xs text-slate-500">›</span>
    </a>
    <a href="#" class="flex items-center justify-between rounded-xl px-3 py-2 transition hover:bg-slate-800">
        <span>Групи</span>
        <span class="text-xs text-slate-500">›</span>
    </a>
    <a href="#" class="flex items-center justify-between rounded-xl px-3 py-2 transition hover:bg-slate-800">
        <span>Матеріали</span>
        <span class="text-xs text-slate-500">›</span>
    </a>
@endsection

@section('content')
    @if (! $teacher)
        <div class="rounded-3xl border border-slate-800 bg-slate-900 p-6 text-sm text-slate-300">
            Немає викладача для відображення. Створіть користувача з роллю <span class="font-semibold text-white">teacher</span> та додайте уроки у Filament.
        </div>
    @else
        <div class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
            <div class="lg:col-span-2 flex flex-wrap justify-end gap-3">
                <a href="{{ route('filament.teacher.pages.dashboard') }}" class="inline-flex items-center rounded-full bg-indigo-500 px-5 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-900/30 transition hover:bg-indigo-400">
                    Відкрити панель керування тестами
                </a>
            </div>
            <section class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg">
                <header class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-white">Найближчі уроки</h2>
                        <p class="text-xs text-slate-400">Оновлено {{ now()->translatedFormat('d MMM') }}</p>
                    </div>
                    <a href="{{ route('dashboard.teacher') }}" class="text-xs font-semibold text-indigo-400">Оновити</a>
                </header>
                <div class="mt-6 space-y-4 text-sm text-slate-300">
                    @forelse ($lessons as $lesson)
                        <article class="rounded-2xl border border-slate-800 bg-slate-800/60 px-4 py-3">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-white">{{ $lesson->title }}</h3>
                                <span class="text-xs text-slate-400">{{ optional($lesson->starts_at)?->format('D H:i') ?? 'без дати' }}</span>
                            </div>
                            <p class="mt-2 text-xs text-slate-400">{{ $lesson->meeting_url ? 'Онлайн' : 'Офлайн' }} · {{ $lesson->duration_minutes }} хв</p>
                        </article>
                    @empty
                        <p class="rounded-2xl border border-dashed border-slate-700 bg-slate-900/40 px-4 py-6 text-center text-xs text-slate-400">Уроків не заплановано.</p>
                    @endforelse
                </div>
            </section>
            <section class="space-y-6">
                <div class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg">
                    <h2 class="text-lg font-semibold text-white">Домашні завдання</h2>
                    <ul class="mt-4 space-y-3 text-xs text-slate-300">
                        @forelse ($pendingHomework as $item)
                            <li class="flex items-center justify-between rounded-2xl bg-slate-800/60 px-4 py-3">
                                <span>{{ $item->student?->username ?? 'Студент' }} · {{ $item->test?->title }}</span>
                                <span class="text-slate-400">{{ __('Очікує перевірки') }}</span>
                            </li>
                        @empty
                            <li class="rounded-2xl border border-dashed border-slate-700 bg-slate-900/40 px-4 py-6 text-center text-xs text-slate-400">
                                Немає домашніх, що очікують перевірки.
                            </li>
                        @endforelse
                    </ul>
                </div>
                <div class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg">
                    <h2 class="text-lg font-semibold text-white">Останні повідомлення</h2>
                    <ul class="mt-4 space-y-3 text-xs text-slate-300">
                        @forelse ($messages as $message)
                            <li class="rounded-2xl bg-slate-800/60 px-4 py-3">
                                <p class="font-semibold text-white">{{ $message->recipient?->username ?? 'Студент' }}</p>
                                @if ($message->recipient && $message->recipient->full_name && $message->recipient->full_name !== $message->recipient->username)
                                    <p class="text-[11px] text-slate-500">ПІБ: {{ $message->recipient->full_name }}</p>
                                @endif
                                <p class="text-slate-400">{{ \Illuminate\Support\Str::limit($message->body, 140) }}</p>
                                <span class="mt-1 block text-[10px] text-slate-500">{{ optional($message->sent_at ?? $message->created_at)?->diffForHumans() }}</span>
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
