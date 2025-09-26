<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Кабінет') — {{ config('app.name', 'English Tutor') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="flex min-h-screen flex-col md:flex-row">
        <aside class="w-full border-b border-slate-800 bg-slate-900 px-6 py-6 md:w-64 md:border-r md:border-b-0">
            <div class="mb-6">
                <a href="{{ route('home') }}" class="text-lg font-semibold text-indigo-400">{{ config('app.name', 'English Tutor') }}</a>
                <p class="mt-1 text-xs text-slate-400">@yield('panel-label')</p>
            </div>
            <nav class="space-y-2 text-sm text-slate-300">
                @yield('sidebar')
                <a href="{{ route('home') }}" class="flex items-center justify-between rounded-xl px-3 py-2 transition hover:bg-slate-800">
                    <span>Повернутися на сайт</span>
                    <span class="text-xs text-slate-500">←</span>
                </a>
            </nav>
        </aside>
        <main class="flex-1 bg-slate-950">
            <header class="border-b border-slate-800 bg-slate-900/70 backdrop-blur px-6 py-5">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 class="text-xl font-semibold text-white">@yield('heading')</h1>
                        <p class="text-xs text-slate-400">@yield('subheading')</p>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        @auth
                            <span class="rounded-full bg-slate-800 px-3 py-1 text-slate-300">{{ auth()->user()->email }}</span>
                            <form method="POST" action="{{ route('logout') }}" class="flex">
                                @csrf
                                <button type="submit" class="rounded-full border border-slate-700 px-3 py-1 text-slate-300 transition hover:border-slate-500">Вийти</button>
                            </form>
                        @endauth
                    </div>
                </div>
            </header>
            <section class="px-6 py-8">
                @yield('content')
            </section>
        </main>
    </div>
    @stack('scripts')
</body>
</html>
