<x-filament::widget>
    <x-filament::card>
        <div class="space-y-6">
            <section>
                <div class="flex items-center justify-between">
                    <h2 class="text-base font-semibold text-slate-100">Заняття на тиждень</h2>
                    <span class="text-xs text-slate-400">16–22 вересня</span>
                </div>
                <ul class="mt-4 space-y-3">
                    @foreach ($lessons as $lesson)
                        <li class="rounded-2xl border border-slate-800 bg-slate-900/60 px-4 py-3">
                            <div class="flex items-center justify-between text-sm text-slate-200">
                                <span class="font-medium">{{ $lesson['title'] }}</span>
                                <span class="text-xs text-slate-400">{{ $lesson['time'] }}</span>
                            </div>
                            <p class="mt-1 text-xs text-slate-400">{{ $lesson['location'] }}</p>
                        </li>
                    @endforeach
                </ul>
            </section>
            <section class="grid gap-4 md:grid-cols-2">
                <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
                    <h3 class="text-sm font-semibold text-slate-100">Домашні завдання</h3>
                    <ul class="mt-3 space-y-2 text-xs text-slate-300">
                        @foreach ($homeworks as $task)
                            <li class="flex items-center justify-between rounded-xl bg-slate-900/40 px-3 py-2">
                                <span>{{ $task['group'] }}</span>
                                <span class="text-slate-400">{{ $task['status'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
                    <h3 class="text-sm font-semibold text-slate-100">Повідомлення</h3>
                    <ul class="mt-3 space-y-2 text-xs text-slate-300">
                        @foreach ($messages as $message)
                            <li class="rounded-xl bg-slate-900/40 px-3 py-2">
                                <p class="font-semibold text-slate-100">{{ $message['name'] }}</p>
                                <p class="text-slate-400">{{ $message['text'] }}</p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </section>
        </div>
    </x-filament::card>
</x-filament::widget>
