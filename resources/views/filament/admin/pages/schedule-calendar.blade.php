<x-filament::page>
    <div class="flex flex-col gap-4">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-500">Викладач</label>
                <select wire:model="teacherId" class="mt-1 block w-56 rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Усі викладачі</option>
                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->full_name ?: $teacher->username }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-500">Початок тижня</label>
                <input type="date" wire:model="startsOn" class="mt-1 block rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-500">Кінець тижня</label>
                <input type="date" wire:model="endsOn" class="mt-1 block rounded-lg border-slate-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="flex items-end gap-2">
                <x-filament::button wire:click="previousWeek" color="gray" icon="heroicon-m-arrow-left">
                    Попередній тиждень
                </x-filament::button>
                <x-filament::button wire:click="nextWeek" color="gray" icon="heroicon-m-arrow-right">
                    Наступний тиждень
                </x-filament::button>
            </div>
        </div>

        @if ($selectedTeacher)
            <x-filament::section>
                <x-slot name="heading">Доступність викладача</x-slot>
                <div class="flex flex-wrap gap-3 text-sm text-slate-500">
                    @forelse ($selectedTeacher->availabilities->sortBy(['weekday', 'starts_at']) as $slot)
                        <span class="rounded-full bg-slate-100 px-3 py-1">{{ \App\Filament\Admin\Resources\TeacherAvailabilityResource::weekdayOptions()[$slot->weekday] ?? $slot->weekday }} · {{ optional($slot->starts_at)->format('H:i') }}–{{ optional($slot->ends_at)->format('H:i') }}</span>
                    @empty
                        <span>Для викладача ще не додано доступних слотів.</span>
                    @endforelse
                </div>
            </x-filament::section>
        @endif

        <div class="grid gap-4 lg:grid-cols-7">
            @foreach ($period as $date)
                @php
                    /** @var \Carbon\Carbon $date */
                    $day = $date->toDateString();
                    $dayLessons = $lessonsByDay[$day] ?? collect();
                @endphp
                <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-semibold text-slate-700">{{ $date->isoFormat('D MMMM, dddd') }}</span>
                        <span class="text-slate-400">{{ $dayLessons->count() }}</span>
                    </div>
                    @php
                        $teachersSummary = $dayLessons->groupBy('teacher_id')->map(function ($lessons) {
                            $teacher = $lessons->first()->teacher;
                            $name = $teacher?->full_name ?: $teacher?->username ?: '—';

                            return $name . ' · ' . $lessons->count();
                        });
                    @endphp
                    @if ($teachersSummary->isNotEmpty())
                        <div class="flex flex-wrap gap-2 text-[11px] uppercase tracking-wide text-slate-500">
                            @foreach ($teachersSummary as $summary)
                                <span class="rounded-full bg-slate-100 px-2 py-0.5">{{ $summary }}</span>
                            @endforeach
                        </div>
                    @endif
                    <div class="flex flex-col gap-3 text-sm text-slate-600">
                        @forelse ($dayLessons as $lesson)
                            <div class="rounded-xl border border-slate-200 bg-slate-50 px-3 py-2">
                                <div class="flex items-center justify-between text-xs uppercase tracking-wide text-slate-400">
                                    <span>{{ optional($lesson->starts_at)->format('H:i') ?? '—' }}</span>
                                    <div class="flex items-center gap-2">
                                        <span class="rounded-full bg-slate-200/70 px-2 py-0.5 text-[11px] uppercase tracking-wide text-slate-600">
                                        {{ $lesson->teacher?->full_name ?? $lesson->teacher?->username ?? '—' }}
                                    </span>
                                        @if ($lesson->series_id)
                                            <span class="text-indigo-500 text-[11px] uppercase">Серія</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-1 font-semibold text-slate-800">{{ $lesson->title }}</div>
                                <div class="flex flex-wrap items-center gap-2 text-[11px] uppercase tracking-wide">
                                    <span class="rounded-full bg-slate-200/70 px-2 py-0.5 text-slate-600">
                                        {{ $lesson->format === 'group' ? 'Групове' : 'Індивідуальне' }}
                                    </span>
                                    @if ($lesson->is_temporary)
                                        <span class="rounded-full bg-amber-200/70 px-2 py-0.5 text-amber-700">Тимчасове</span>
                                    @endif
                                    @if ($lesson->group)
                                        <span class="rounded-full bg-indigo-200/60 px-2 py-0.5 text-indigo-700">Група: {{ $lesson->group->name }}</span>
                                    @endif
                                </div>
                                @if ($lesson->students->isNotEmpty())
                                    <div class="mt-2 flex flex-wrap gap-1 text-[11px] text-emerald-600">
                                        @foreach ($lesson->students as $student)
                                            <span class="rounded-full bg-emerald-50 px-2 py-0.5">{{ $student->full_name ?: $student->username }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-slate-200 px-3 py-4 text-center text-xs text-slate-400">
                                Без занять
                            </div>
                        @endforelse
                    </div>
                    <div>
                        <x-filament::button color="gray" size="xs" wire:click="openDay('{{ $day }}')">
                            Переглянути день
                        </x-filament::button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <x-filament::modal
        id="day-modal"
        close-button
        width="4xl"
    >
        <x-slot name="header">
            {{ $focusedDate ? \Carbon\Carbon::parse($focusedDate)->isoFormat('D MMMM, dddd') : '' }} — завантаженість
        </x-slot>

        @php($pagination = $this->modalPaginated)

        <div class="space-y-4">
            @forelse ($pagination['items'] as $lesson)
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <div class="flex items-center justify-between text-sm text-slate-500">
                        <span class="font-semibold text-slate-700">{{ $lesson['time'] }} • {{ $lesson['duration'] }} хв</span>
                        <span class="rounded-full bg-slate-200/70 px-2 py-0.5 text-xs uppercase tracking-wide text-slate-600">{{ $lesson['teacher'] }}</span>
                    </div>
                    <div class="mt-2 text-lg font-semibold text-slate-800">{{ $lesson['title'] }}</div>
                    <div class="flex flex-wrap gap-2 text-xs uppercase tracking-wide text-slate-500">
                        <span class="rounded-full bg-slate-200/70 px-2 py-0.5">{{ $lesson['format'] }}</span>
                        @if ($lesson['is_temporary'])
                            <span class="rounded-full bg-amber-200/70 px-2 py-0.5 text-amber-700">Тимчасове</span>
                        @endif
                        @if ($lesson['group'])
                            <span class="rounded-full bg-indigo-200/60 px-2 py-0.5 text-indigo-700">Група: {{ $lesson['group'] }}</span>
                        @endif
                        @if ($lesson['is_series'])
                            <span class="rounded-full bg-indigo-200/60 px-2 py-0.5 text-indigo-700">Серія</span>
                        @endif
                    </div>
                    @if (! empty($lesson['students']))
                        <div class="mt-2 flex flex-wrap gap-1 text-sm text-emerald-600">
                            @foreach ($lesson['students'] as $student)
                                <span class="rounded-full bg-emerald-50 px-2 py-0.5">{{ $student['name'] }}</span>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-2 text-sm text-slate-500">Учні: —</div>
                    @endif
                </div>
            @empty
                <div class="text-center text-sm text-slate-500">На цей день не заплановано уроків.</div>
            @endforelse
        </div>

        <x-slot name="footer">
            <div class="flex w-full items-center justify-between">
                <div class="text-xs text-slate-500">
                    Сторінка {{ $modalPage }} із {{ $pagination['pages'] }}
                </div>
                <div class="flex gap-2">
                    <x-filament::button color="gray" size="sm" wire:click="previousModalPage" :disabled="$modalPage === 1">
                        Назад
                    </x-filament::button>
                    <x-filament::button color="gray" size="sm" wire:click="nextModalPage" :disabled="$modalPage >= $pagination['pages']">
                        Далі
                    </x-filament::button>
                    <x-filament::button color="gray" wire:click="closeDay">Закрити</x-filament::button>
                </div>
            </div>
        </x-slot>
    </x-filament::modal>
</x-filament::page>
