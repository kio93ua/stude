@extends('layouts.dashboard')

@section('title', 'Мій словник')
@section('panel-label', 'Учень')
@section('heading', 'Особистий словник')
@section('subheading', 'Додавайте нові слова, переклад та приклади')

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
    <a href="{{ route('dashboard.student.vocabulary.index') }}" class="flex items-center justify-between rounded-xl px-3 py-2 text-indigo-300 transition hover:bg-slate-800">
        <span>Словник</span>
        <span class="text-xs text-slate-500">●</span>
    </a>
@endsection

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

        <section class="rounded-3xl border border-slate-800 bg-slate-900 p-6 shadow-lg">
            <h2 class="text-lg font-semibold text-white">Додати нове слово</h2>
            <p class="mt-1 text-xs text-slate-400">Заповніть усі поля, щоб запам'ятати нову лексику. Ви завжди можете відредагувати записи пізніше.</p>
            <form method="POST" action="{{ route('dashboard.student.vocabulary.store') }}" class="mt-5 grid gap-4 md:grid-cols-2">
                @csrf
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-400">Англійське слово</label>
                    <input type="text" name="term" value="{{ old('term') }}" required maxlength="120" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white focus:border-indigo-400 focus:outline-none" placeholder="Наприклад, breakthrough">
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-400">Переклад</label>
                    <input type="text" name="translation" value="{{ old('translation') }}" required maxlength="255" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white focus:border-indigo-400 focus:outline-none" placeholder="Прорив">
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-400">Визначення</label>
                    <textarea name="definition" rows="3" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white focus:border-indigo-400 focus:outline-none" placeholder="Short explanation...">{{ old('definition') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-400">Приклад речення</label>
                    <textarea name="example" rows="3" class="mt-2 w-full rounded-2xl border border-slate-700 bg-slate-900 px-4 py-3 text-sm text-white focus:border-indigo-400 focus:outline-none" placeholder="Use the word in context...">{{ old('example') }}</textarea>
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <button type="submit" class="inline-flex items-center rounded-full bg-emerald-500 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-400">Зберегти слово</button>
                </div>
            </form>
        </section>

        <section class="space-y-4">
            <header class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-white">Мій словник</h2>
                    <p class="text-xs text-slate-400">Записів: {{ $entries->total() }}</p>
                </div>
                <form method="GET" action="{{ route('dashboard.student.vocabulary.index') }}" class="flex flex-wrap items-center gap-2">
                    <label for="vocabulary-search" class="text-xs font-semibold uppercase tracking-wide text-slate-400">Пошук</label>
                    <input id="vocabulary-search" name="q" value="{{ $search }}" placeholder="Слово або переклад" class="w-52 rounded-2xl border border-slate-700 bg-slate-900 px-4 py-2 text-sm text-white focus:border-indigo-400 focus:outline-none">
                    <button type="submit" class="inline-flex items-center rounded-full bg-indigo-500 px-4 py-2 text-xs font-semibold text-white transition hover:bg-indigo-400">Знайти</button>
                    @if ($search)
                        <a href="{{ route('dashboard.student.vocabulary.index') }}" class="text-xs font-semibold text-slate-400 hover:text-white">Скинути</a>
                    @endif
                </form>
            </header>

            @if ($entries->isEmpty())
                <div class="rounded-3xl border border-dashed border-slate-700 bg-slate-900/40 px-6 py-12 text-center text-sm text-slate-400">
                    Словник порожній. Додайте нові слова за допомогою форми вище.
                </div>
            @else
                <div class="overflow-x-auto rounded-3xl border border-slate-800 bg-slate-900 shadow-lg">
                    <table class="min-w-full divide-y divide-slate-800 text-sm text-slate-300">
                        <thead class="bg-slate-900/70 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3 text-left">Слово</th>
                                <th class="px-4 py-3 text-left">Переклад</th>
                                <th class="px-4 py-3 text-left">Визначення</th>
                                <th class="px-4 py-3 text-left">Приклад</th>
                                <th class="px-4 py-3 text-left">Оновлено</th>
                                <th class="px-4 py-3 text-left">Дії</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800">
                            @foreach ($entries as $entry)
                                <tr class="align-top">
                                    <td class="px-4 py-4 font-semibold text-white">{{ $entry->term }}</td>
                                    <td class="px-4 py-4 text-indigo-300">{{ $entry->translation }}</td>
                                    <td class="px-4 py-4 text-slate-300">{{ \Illuminate\Support\Str::limit($entry->definition, 120) }}</td>
                                    <td class="px-4 py-4 text-slate-400 italic">{{ \Illuminate\Support\Str::limit($entry->example, 120) }}</td>
                                    <td class="px-4 py-4 text-[11px] text-slate-500">
                                        {{ optional($entry->updated_at)?->diffForHumans() ?? 'щойно' }}
                                        @if ($entry->lastUpdatedBy && $entry->lastUpdatedBy->id !== $student->id)
                                            <span class="block text-[10px] text-indigo-400">редагував викладач</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <details class="group">
                                            <summary class="cursor-pointer rounded-full bg-slate-800 px-4 py-2 text-xs font-semibold text-indigo-300 transition group-open:bg-indigo-500 group-open:text-white">Редагувати</summary>
                                            <div class="mt-3 space-y-3 rounded-2xl border border-slate-700 bg-slate-900/80 p-4">
                                                <form method="POST" action="{{ route('dashboard.student.vocabulary.update', $entry) }}" class="space-y-3">
                                                    @csrf
                                                    @method('PUT')
                                                    <div>
                                                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-400">Слово</label>
                                                        <input type="text" name="term" value="{{ old('term', $entry->term) }}" required maxlength="120" class="mt-1 w-full rounded-2xl border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-white focus:border-indigo-400 focus:outline-none">
                                                    </div>
                                                    <div>
                                                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-400">Переклад</label>
                                                        <input type="text" name="translation" value="{{ old('translation', $entry->translation) }}" required maxlength="255" class="mt-1 w-full rounded-2xl border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-white focus:border-indigo-400 focus:outline-none">
                                                    </div>
                                                    <div>
                                                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-400">Визначення</label>
                                                        <textarea name="definition" rows="3" class="mt-1 w-full rounded-2xl border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-white focus:border-indigo-400 focus:outline-none">{{ old('definition', $entry->definition) }}</textarea>
                                                    </div>
                                                    <div>
                                                        <label class="text-xs font-semibold uppercase tracking-wide text-slate-400">Приклад</label>
                                                        <textarea name="example" rows="3" class="mt-1 w-full rounded-2xl border border-slate-700 bg-slate-900 px-3 py-2 text-sm text-white focus:border-indigo-400 focus:outline-none">{{ old('example', $entry->example) }}</textarea>
                                                    </div>
                                                    <div class="flex flex-wrap items-center gap-3">
                                                        <button type="submit" class="inline-flex items-center rounded-full bg-indigo-500 px-4 py-2 text-xs font-semibold text-white transition hover:bg-indigo-400">Зберегти</button>
                                                    </div>
                                                </form>
                                                <form method="POST" action="{{ route('dashboard.student.vocabulary.destroy', $entry) }}" onsubmit="return confirm('Видалити запис \"{{ $entry->term }}\" зі словника?');" class="inline-flex">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center rounded-full bg-rose-500 px-4 py-2 text-xs font-semibold text-white transition hover:bg-rose-400">Видалити</button>
                                                </form>
                                            </div>
                                        </details>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pt-4">
                    {{ $entries->links('pagination::tailwind') }}
                </div>
            @endif
        </section>
    </div>
@endsection
