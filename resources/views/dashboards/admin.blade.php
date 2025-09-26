@extends('layouts.dashboard')

@section('title', 'Адміністративна панель')
@section('panel-label', 'Адміністратор')
@section('heading', 'Адмін-панель')
@section('subheading', 'Огляд показників школи та управління командами')

@section('sidebar')
    <a href="{{ route('dashboard.admin') }}" class="flex items-center justify-between rounded-xl px-3 py-2 transition hover:bg-slate-800 {{ request()->routeIs('dashboard.admin') ? 'text-indigo-300' : 'text-slate-300' }}">
        <span>Дашборд</span>
        <span class="text-xs text-slate-500">{{ request()->routeIs('dashboard.admin') ? '●' : '›' }}</span>
    </a>
    <a href="{{ route('filament.admin.resources.students.index') }}" class="flex items-center justify-between rounded-xl px-3 py-2 transition hover:bg-slate-800 {{ request()->routeIs('filament.admin.resources.students.*') ? 'text-indigo-300' : 'text-slate-300' }}">
        <span>Студенти</span>
        <span class="text-xs text-slate-500">›</span>
    </a>
    <a href="{{ route('filament.admin.resources.teachers.index') }}" class="flex items-center justify-between rounded-xl px-3 py-2 transition hover:bg-slate-800 {{ request()->routeIs('filament.admin.resources.teachers.*') ? 'text-indigo-300' : 'text-slate-300' }}">
        <span>Викладачі</span>
        <span class="text-xs text-slate-500">›</span>
    </a>
    <a href="#" class="flex items-center justify-between rounded-xl px-3 py-2 transition hover:bg-slate-800">
        <span>Розклад</span>
        <span class="text-xs text-slate-500">›</span>
    </a>
    <a href="#" class="flex items-center justify-between rounded-xl px-3 py-2 transition hover:bg-slate-800">
        <span>Фінанси</span>
        <span class="text-xs text-slate-500">›</span>
    </a>
@endsection

@section('content')
    <div class="grid gap-6 md:grid-cols-3">
        <div class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg shadow-slate-900/40">
            <p class="text-xs uppercase tracking-wide text-slate-400">Студенти</p>
            <p class="mt-3 text-3xl font-semibold text-white">{{ $totals['students'] }}</p>
            <p class="mt-2 text-xs text-emerald-400">Активні акаунти</p>
        </div>
        <div class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg shadow-slate-900/40">
            <p class="text-xs uppercase tracking-wide text-slate-400">Викладачі</p>
            <p class="mt-3 text-3xl font-semibold text-white">{{ $totals['teachers'] }}</p>
            <p class="mt-2 text-xs text-emerald-400">У команді</p>
        </div>
        <div class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg shadow-slate-900/40">
            <p class="text-xs uppercase tracking-wide text-slate-400">Уроки цього місяця</p>
            <p class="mt-3 text-3xl font-semibold text-white">{{ $monthlyLessons }}</p>
            <p class="mt-2 text-xs text-emerald-400">Заплановано</p>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <section class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg">
            <h2 class="text-lg font-semibold text-white">Ближчі уроки</h2>
            <ul class="mt-4 space-y-4 text-sm text-slate-300">
                @forelse ($upcomingLessons as $lesson)
                    <li class="flex items-center justify-between rounded-2xl bg-slate-800/60 px-4 py-3">
                        <div>
                            <p class="font-semibold text-white">{{ $lesson->title }}</p>
                            <p class="text-xs text-slate-400">Викладач: {{ $lesson->teacher?->full_name ?? '—' }}</p>
                        </div>
                        <span class="text-xs text-slate-400">{{ optional($lesson->starts_at)?->format('d.m H:i') }}</span>
                    </li>
                @empty
                    <li class="rounded-2xl border border-dashed border-slate-700 bg-slate-900/40 px-4 py-6 text-center text-xs text-slate-400">Немає запланованих уроків.</li>
                @endforelse
            </ul>
        </section>
        <section class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg">
            <h2 class="text-lg font-semibold text-white">Нові зарахування</h2>
            <ul class="mt-4 space-y-4 text-sm text-slate-300">
                @forelse ($recentEnrollments as $enrollment)
                    <li class="flex items-center justify-between rounded-2xl bg-slate-800/60 px-4 py-3">
                        <div>
                            <p class="font-semibold text-white">{{ $enrollment->student?->username ?? 'Студент' }}</p>
                            @if ($enrollment->student && $enrollment->student->full_name && $enrollment->student->full_name !== $enrollment->student->username)
                                <p class="text-xs text-slate-500">Ім'я: {{ $enrollment->student->full_name }}</p>
                            @endif
                            <p class="text-xs text-slate-400">{{ $enrollment->lesson?->title ?? 'Урок' }}</p>
                        </div>
                        <span class="text-xs text-slate-400">{{ $enrollment->created_at?->diffForHumans() }}</span>
                    </li>
                @empty
                    <li class="rounded-2xl border border-dashed border-slate-700 bg-slate-900/40 px-4 py-6 text-center text-xs text-slate-400">Ще немає зарахувань.</li>
                @endforelse
            </ul>
        </section>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <section class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg">
            <h2 class="text-lg фонt-semibold text-white">Повідомлення</h2>
            <ul class="mt-4 space-y-4 text-sm text-slate-300">
                @forelse ($recentMessages as $message)
                    <li class="rounded-2xl bg-slate-800/60 px-4 py-3">
                        <p class="font-semibold text-white">{{ $message->sender?->username ?? 'Надіслано' }} → {{ $message->recipient?->username ?? 'Адміністрація' }}</p>
                        @if ($message->sender && $message->sender->full_name && $message->sender->full_name !== $message->sender->username)
                            <p class="text-[11px] text-slate-500">Відправник: {{ $message->sender->full_name }}</p>
                        @endif
                        @if ($message->recipient && $message->recipient->full_name && $message->recipient->full_name !== $message->recipient->username)
                            <p class="text-[11px] text-slate-500">Отримувач: {{ $message->recipient->full_name }}</p>
                        @endif
                        <p class="text-xs text-slate-400">{{ \Illuminate\Support\Str::limit($message->body, 160) }}</p>
                        <span class="mt-1 block text-[10px] text-slate-500">{{ optional($message->sent_at ?? $message->created_at)?->diffForHumans() }}</span>
                    </li>
                @empty
                    <li class="rounded-2xl border border-dashed border-slate-700 bg-slate-900/40 px-4 py-6 text-center text-xs text-slate-400">Поки що немає повідомлень.</li>
                @endforelse
            </ul>
        </section>
        <section class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg">
            <h2 class="text-lg font-semibold text-white">Статистика за місяць</h2>
            <dl class="mt-4 space-y-3 text-sm text-slate-300">
                <div class="flex items-center justify-between">
                    <dt>Нові зарахування</dt>
                    <dd>{{ $monthlyEnrollments }}</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt>Перевірені тести</dt>
                    <dd>{{ $completionRate }}</dd>
                </div>
            </dl>
        </section>
    </div>
@endsection
