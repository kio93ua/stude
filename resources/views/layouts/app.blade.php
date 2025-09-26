<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'English Tutor') }}</title>
    <meta name="description" content="Personal English tutor helping students gain confidence in speaking, grammar, and exam preparation.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900">
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur shadow-sm">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <a href="#hero" class="text-lg font-semibold text-indigo-600">{{ config('app.name', 'English Tutor') }}</a>
            <nav class="hidden gap-6 text-sm font-medium text-slate-600 md:flex">
                <a href="#services" class="hover:text-indigo-600">Послуги</a>
                <a href="#about" class="hover:text-indigo-600">Про мене</a>
                <a href="#approach" class="hover:text-indigo-600">Підхід</a>
                <a href="#testimonials" class="hover:text-indigo-600">Відгуки</a>
                <a href="#contact" class="hover:text-indigo-600">Запис</a>
            </nav>
            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="rounded-full border border-indigo-200 px-4 py-2 text-sm font-semibold text-indigo-600 transition hover:border-indigo-400">Вхід / Кабінет</a>
                <a href="#contact" class="rounded-full bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-indigo-200 transition hover:bg-indigo-500">Записатися</a>
            </div>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl flex-col gap-4 px-6 py-8 text-sm text-slate-600 md:flex-row md:items-center md:justify-between">
            <p>&copy; {{ now()->year }} {{ config('app.name', 'English Tutor') }}. Усі права захищено.</p>
            <div class="flex gap-4">
                <a href="mailto:hello@studytutor.com" class="hover:text-indigo-600">hello@studytutor.com</a>
                <a href="tel:+380671234567" class="hover:text-indigo-600">+38 (067) 123 45 67</a>
            </div>
        </div>
    </footer>
    @stack('scripts')
</body>
</html>
